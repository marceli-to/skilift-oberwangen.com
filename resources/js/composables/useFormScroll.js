export function useFormScroll(selector = 'form') {
  const scrollToForm = () => {
    const formElement = document.querySelector(selector);
    
    if (formElement) {
      window.scrollTo({
        top: formElement.offsetTop,
        behavior: 'smooth'
      });
    }
  };

  return {
    scrollToForm
  };
}
