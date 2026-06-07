import http from 'k6/http';
import { sleep, check } from 'k6';
import { Trend } from 'k6/metrics';

// ---------------------------------------------------------------------------
// Custom metric: track menu page response times separately (most DB-heavy)
// ---------------------------------------------------------------------------
const menuTrend = new Trend('menu_page_duration', true);

// ---------------------------------------------------------------------------
// Load profile: ramp-up, stress plateau, cooldown
// Tune stages below to adjust the aggression of the test
// ---------------------------------------------------------------------------
export const options = {
  stages: [
    { duration: '30s', target: 50  },   // 0→50  concurrent users
    { duration: '30s', target: 100 },   // 50→100 concurrent users
    { duration: '2m',  target: 100 },   // hold at 100  (stress plateau)
    { duration: '15s', target: 0   },   // 100→0 cooldown
  ],

  // Strict failure conditions — adjust p(95) threshold if needed
  thresholds: {
    http_req_failed:   ['rate < 0.01'],   // fewer than 1% failures
    http_req_duration: ['p(95) < 2500'],  // 95th percentile under 2.5 s
  },
};

// ---------------------------------------------------------------------------
// Configuration — change BASE_URL when testing a different environment
// ---------------------------------------------------------------------------
const BASE_URL = 'http://localhost/rest_menu';

// ---- GET routes with traffic weights (sum = 90 for GET; POST is 10) ----
const GET_ROUTES = [
  { url: '/',                                                  weight: 15, name: 'Homepage' },
  { url: '/about',                                             weight: 12, name: 'About' },
  { url: '/menu',                                              weight: 15, name: 'Menu (default)' },
  { url: '/menu?category=OUR+FAMOUS+PIES',                     weight: 12, name: 'Menu: Pies' },
  { url: '/menu?category=COMBOS',                              weight: 12, name: 'Menu: Combos' },
  { url: '/menu?category=WRAPS',                               weight: 12, name: 'Menu: Wraps' },
  { url: '/menu?category=DIPS+%26+APPETIZERS',                 weight: 12, name: 'Menu: Dips' },
];

// ---- Rotating User-Agent strings (realistic Chrome UA variants) ----
const USER_AGENTS = [
  // Chrome 125 on Windows 10
  'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
  // Chrome 125 on macOS 14.5 (Sonoma)
  'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
  // Chrome 125 on Android 14 (Pixel 8)
  'Mozilla/5.0 (Linux; Android 14; Pixel 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.6422.77 Mobile Safari/537.36',
  // Chrome 125 on iOS 17.5 (iPhone)
  'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/125.0.6422.80 Mobile/15E148 Safari/604.1',
];

// ---- Sample data for randomised POST /save_order payloads ----
const FIRST_NAMES = ['Ahmed', 'Sara', 'Omar', 'Layla', 'Hassan', 'Nour', 'Youssef', 'Mona', 'Khaled', 'Fatima'];
const LAST_NAMES  = ['Al-Rashid', 'Hussein', 'Khalil', 'Mahmoud', 'Ibrahim', 'Saeed', 'Darwish', 'Nassar', 'Saleh', 'Haddad'];
const NOTES       = [null, 'No onions please', 'Extra garlic sauce', 'Less spicy', null, null, 'Well done', null];

// prettier-ignore
const MENU_ITEMS = [
  { name: 'Cheese Pie',         price: 3.99  },
  { name: 'Spinach Fatayer',    price: 2.99  },
  { name: 'Meat Pie',           price: 4.50  },
  { name: 'Chicken Shawarma',   price: 5.99  },
  { name: 'Beef Kebab Plate',   price: 9.99  },
  { name: 'Falafel Wrap',       price: 4.25  },
  { name: 'Hummus Plate',       price: 3.50  },
  { name: 'Mixed Grill Combo',  price: 12.99 },
  { name: 'Baklava Dessert',    price: 5.00  },
  { name: 'Mint Lemonade',      price: 2.50  },
];

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

/**
 * Build a weighted cumulative array from GET_ROUTES so we can pick a random
 * route proportional to its weight without hard-coding percent branches.
 */
function buildWeightedRoutes(routes) {
  const weighted = [];
  for (const r of routes) {
    for (let i = 0; i < r.weight; i++) {
      weighted.push(r);
    }
  }
  return weighted;
}

const WEIGHTED_GET = buildWeightedRoutes(GET_ROUTES);

/**
 * Generate a randomised but valid POST body for /save_order.
 */
function randomOrderPayload() {
  const itemCount = Math.floor(Math.random() * 4) + 1; // 1-4 items
  const selected = [];
  let total = 0;

  for (let i = 0; i < itemCount; i++) {
    const item = MENU_ITEMS[Math.floor(Math.random() * MENU_ITEMS.length)];
    selected.push({ name: item.name, price: item.price });
    total += item.price;
  }

  return JSON.stringify({
    customer_name:  `${FIRST_NAMES[Math.floor(Math.random() * FIRST_NAMES.length)]} ${LAST_NAMES[Math.floor(Math.random() * LAST_NAMES.length)]}`,
    customer_phone: `+201${Math.floor(10000000 + Math.random() * 90000000)}`,
    items:          selected,
    total_usd:      Math.round(total * 100) / 100,
    notes:          NOTES[Math.floor(Math.random() * NOTES.length)],
  });
}

/**
 * Pick a random User-Agent and return a headers object.
 */
function randomHeaders() {
  const ua = USER_AGENTS[Math.floor(Math.random() * USER_AGENTS.length)];
  return {
    headers: {
      'User-Agent':      ua,
      'Accept':          'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
      'Accept-Language': 'en-US,en;q=0.5',
    },
  };
}

// ---------------------------------------------------------------------------
// Main test function — executed once per iteration per VU
// ---------------------------------------------------------------------------
export default function () {
  // ~10% chance of hitting the POST checkout endpoint
  if (Math.random() < 0.1) {
    const url  = `${BASE_URL}/save_order`;
    const body = randomOrderPayload();

    const res = http.post(url, body, {
      headers: {
        'User-Agent':   USER_AGENTS[Math.floor(Math.random() * USER_AGENTS.length)],
        'Content-Type': 'application/json',
        'Accept':       'application/json',
      },
    });

    check(res, { 'POST /save_order → 200': (r) => r.status === 200 });

    if (res.status !== 200) {
      console.log(`FAIL [POST] /save_order — HTTP ${res.status} — ${url}`);
      console.log(`Body:   ${body}`);
      console.log(`Resp:   ${res.body.substring(0, 500)}`);
    }

    sleep(Math.random() * 3 + 2); // 2-5 s think time
    return;
  }

  // Pick a random GET route (weighted)
  const route = WEIGHTED_GET[Math.floor(Math.random() * WEIGHTED_GET.length)];
  const url   = `${BASE_URL}${route.url}`;
  const params = randomHeaders();

  const res = http.get(url, params);

  // Record custom trend for menu routes
  if (route.url.startsWith('/menu')) {
    menuTrend.add(res.timings.duration);
  }

  check(res, { [`${route.name} → 200`]: (r) => r.status === 200 });

  if (res.status !== 200) {
    console.log(`FAIL [GET] ${route.name} — HTTP ${res.status} — ${url}`);
    if (res.error) {
      console.log(`Error:  ${res.error}`);
    }
    if (res.body) {
      console.log(`Body:   ${res.body.substring(0, 500)}`);
    }
  }

  sleep(Math.random() * 3 + 2); // human think time
}
