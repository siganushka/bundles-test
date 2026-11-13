import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://symfony.com/bundles/StimulusBundle/current/index.html#lazy-stimulus-controllers
*/

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static values = {
    url: String,
  }

  optionsChanged(event) {
    console.log('optionsChanged...', event)

    const variantsEntries = this.element.querySelectorAll('[data-siganushka-generic-collection-target="entry"]')
    const variants = Array.from(variantsEntries).map(item => {
      const price = item.querySelector('input[name*="[price]"]').value
      const stock = item.querySelector('input[name*="[stock]"]').value
      const enabled = item.querySelector('input[name*="[enabled]"]')?.checked
      return { price, stock, enabled }
    })

    const body = JSON.stringify({ options: event.detail.options, variants })
    console.log('body...', body)

    fetch(this.urlValue, {
      method: 'POST',
      body,
      headers: { Accept: 'application/json' },
    }).then(async response => {
      this.element.outerHTML = await response.text()
    })
  }
}
