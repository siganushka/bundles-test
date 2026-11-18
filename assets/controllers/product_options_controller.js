import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://symfony.com/bundles/StimulusBundle/current/index.html#lazy-stimulus-controllers
*/

/* stimulusFetch: 'eager' */
export default class extends Controller {
  autocompleteConnect(event) {
    const { tomSelect } = event.detail
    tomSelect.on('change', (value) => {
      console.log('change...', value)
      this.update()
    })
  }

  added(event) {
    console.log('added...', event.detail)
    this.update()
  }

  deleted(event) {
    console.log('deleted...', event.detail)
    this.update()
  }

  update() {
    const optionsEntry = this.element.querySelectorAll('[data-siganushka-generic-collection-target="entry"]')
    const options = Array.from(optionsEntry).map(item => {
      const name = item.querySelector('input[name*="[name]"]').value
      const values = item.querySelector('input[name*="[values]"]').value
      return { name, values }
    })

    this.dispatch('changed', { detail: { options } })
  }
}
