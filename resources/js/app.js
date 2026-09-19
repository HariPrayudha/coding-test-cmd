import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Helper: Format Rupiah string
window.formatRupiah = function (number) {
    if (isNaN(number) || number === null || number === '') return 'Rp 0';
    return 'Rp ' + Math.round(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

// Helper: Parse currency formatted string to number
window.parseCurrency = function (val) {
    if (!val) return 0;
    const clean = val.toString().replace(/[^\d]/g, '');
    return clean ? parseInt(clean, 10) : 0;
};

Alpine.start();
