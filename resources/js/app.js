const allocationForms = document.querySelectorAll('[data-allocation-form]')

allocationForms.forEach(form => {
  form.addEventListener('submit', event => {
    if (form.dataset.submitting === 'true') {
      event.preventDefault()

      return
    }

    const message = form.dataset.confirmation

    if (message && !window.confirm(message)) {
      event.preventDefault()

      return
    }

    form.dataset.submitting = 'true'

    const submitButton = form.querySelector('button[type="submit"]')

    if (submitButton) {
      submitButton.disabled = true
      submitButton.textContent =
        submitButton.dataset.submittingText ?? 'Gerando...'
      submitButton.classList.add('cursor-not-allowed', 'opacity-60')
    }
  })
})
