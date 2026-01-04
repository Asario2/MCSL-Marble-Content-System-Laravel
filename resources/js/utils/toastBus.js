import { reactive } from 'vue';

export const toastBus = reactive({
  toasts: [],
  idCounter: 0,

  emit({ message, type = 'info', duration = 4000 }) {
    const toast = {
      id: this.idCounter++,
      message,
      type,
      duration,
      show: true
    };

    this.toasts.push(toast);

    if (duration > 0) {
      setTimeout(() => this.removeToast(toast.id), duration);
    }
  },

  removeToast(id) {
    const i = this.toasts.findIndex(t => t.id === id);
    if (i !== -1) this.toasts[i].show = false;

    setTimeout(() => {
      this.toasts = this.toasts.filter(t => t.id !== id);
    }, 200);
  }
});

// Legacy / Debug
window.toastBus = toastBus
