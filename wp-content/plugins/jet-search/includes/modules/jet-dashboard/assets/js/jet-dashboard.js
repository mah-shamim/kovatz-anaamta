'use strict';

const { __, _x, _n, _nx, sprintf } = wp.i18n;

window.JetDashboardEventBus = new Vue();

window.JetDasboard = new JetDasboardClass();

//window.JetDasboard.initVueComponents();

window.JetDasboardPageInstance = JetDasboard.initDashboardPageInstance();
