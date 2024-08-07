require('./bootstrap');
require('./sidebar');
window.$ = window.jQuery = require('jquery'); // <-- main, not 'slim'
require('jquery-ui');
window.Popper = require('popper.js');
require('./navbar');
require('slick-carousel');
require('./slider');
require('@fortawesome/fontawesome-free');
require('select2')
window.ZXing = require('@zxing/library');
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})
