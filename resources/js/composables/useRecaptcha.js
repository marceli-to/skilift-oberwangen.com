import { ref, onMounted } from 'vue';

export function useRecaptcha() {
  const isReady = ref(false);
  const isLoading = ref(false);

  const loadRecaptchaScript = () => {
    return new Promise((resolve, reject) => {
      if (window.grecaptcha) {
        isReady.value = true;
        resolve();
        return;
      }

      const script = document.createElement('script');
      script.src = `https://www.google.com/recaptcha/api.js?render=${import.meta.env.VITE_RECAPTCHA_SITE_KEY}`;
      script.async = true;
      script.defer = true;

      script.onload = () => {
        window.grecaptcha.ready(() => {
          isReady.value = true;
          resolve();
        });
      };

      script.onerror = () => {
        reject(new Error('Failed to load reCAPTCHA script'));
      };

      document.head.appendChild(script);
    });
  };

  const executeRecaptcha = async (action = 'submit') => {
    if (!isReady.value) {
      await loadRecaptchaScript();
    }

    try {
      isLoading.value = true;
      const token = await window.grecaptcha.execute(
        import.meta.env.VITE_RECAPTCHA_SITE_KEY,
        { action }
      );
      return token;
    } catch (error) {
      console.error('reCAPTCHA execution error:', error);
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  onMounted(() => {
    loadRecaptchaScript();
  });

  return {
    isReady,
    isLoading,
    executeRecaptcha,
  };
}
