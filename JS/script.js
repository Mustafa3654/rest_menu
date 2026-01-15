const lbpInput = document.getElementById('price-lbp');
const usdInput = document.getElementById('price-usd');
const rate = 90000;
let updatingform=null;
lbpInput.addEventListener('input', () =>{
    if (updatingform == 'usd') return;
    updatingform = 'lbp';
    const lbp = parseFloat(lbpInput.value);
    usdInput.value=!isNaN(lbp) ? (lbp/rate).toFixed(2): '';
    updatingform=null;
});
usdInput.addEventListener('input', () =>{
    if (updatingform == 'lbp') return;
    updatingform = 'usd';
    const usd = parseFloat(usdInput.value);
    lbpInput.value=!isNaN(usd) ? Math.round(usd*rate): '';
    updatingform=null;
});

