export default function popup() {
  return {
    isVisible: false,

    init() {
      const lastSeen = localStorage.getItem('popup_last_seen');
      const now = Date.now();
      const limit = 2 * 60 * 60 * 1000;

      if (!lastSeen || (now - parseInt(lastSeen)) > limit) {
        this.isVisible = true;
      }
    },

    close() {
      this.isVisible = false;
      localStorage.setItem('popup_last_seen', Date.now().toString());
    }
  };
}