import './bootstrap';
import Alpine from 'alpinejs';

Alpine.store('paymentFeedback', {
    open: false,
    status: 'success', // 'success' | 'failed' | 'pending'
    title: '',
    message: '',
    amount: '',
    orderId: '',
    txType: '',
    timestamp: '',
    actionUrl: '',
    actionText: '',
    reason: '',
    show(payload) {
        if (!payload) return;
        this.status = payload.status || 'success';
        this.title = payload.title || (this.status === 'success' ? 'Pembayaran Berhasil!' : (this.status === 'pending' ? 'Menunggu Pembayaran' : 'Pembayaran Gagal'));
        this.message = payload.message || '';
        this.amount = payload.amount || '';
        this.orderId = payload.orderId || payload.order_id || '';
        this.txType = payload.txType || payload.tx_type || '';
        this.timestamp = payload.timestamp || new Date().toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) + ' WIB';
        this.actionUrl = payload.actionUrl || payload.action_url || '';
        this.actionText = payload.actionText || payload.action_text || '';
        this.reason = payload.reason || '';
        this.open = true;
    },
    close() {
        this.open = false;
    }
});

window.Alpine = Alpine;
Alpine.start();

window.showPaymentSuccess = function(details) {
    if (window.Alpine && window.Alpine.store('paymentFeedback')) {
        window.Alpine.store('paymentFeedback').show({ status: 'success', ...details });
    }
};

window.showPaymentFailed = function(details) {
    if (window.Alpine && window.Alpine.store('paymentFeedback')) {
        window.Alpine.store('paymentFeedback').show({ status: 'failed', ...details });
    }
};

window.showPaymentPending = function(details) {
    if (window.Alpine && window.Alpine.store('paymentFeedback')) {
        window.Alpine.store('paymentFeedback').show({ status: 'pending', ...details });
    }
};

