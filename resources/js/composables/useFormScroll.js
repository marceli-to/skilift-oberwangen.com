export function useFormScroll(selector = 'form') {
  const scrollToForm = () => {
    const formElement = document.querySelector(selector);

    if (formElement) {
      const elementPosition = formElement.getBoundingClientRect().top;
      const offsetPosition = elementPosition + window.pageYOffset;

      window.scrollTo({
        top: offsetPosition,
        behavior: 'smooth'
      });
    }
  };

  return {
    scrollToForm
  };
}
