import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://symfony.com/bundles/StimulusBundle/current/index.html#lazy-stimulus-controllers
*/

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  initialize() {
    // Called once when the controller is first instantiated (per element)

    // Here you can initialize variables, create scoped callables for event
    // listeners, instantiate external libraries, etc.
    // this._fooBar = this.fooBar.bind(this)
  }

  connect() {
    // Called every time the controller is connected to the DOM
    // (on page load, when it's added to the DOM, moved in the DOM, etc.)

    // Here you can add event listeners on the element or target elements,
    // add or remove classes, attributes, dispatch custom events, etc.
    // this.fooTarget.addEventListener('click', this._fooBar)
  }

  // Add custom controller actions here
  // fooBar() { this.fooTarget.classList.toggle(this.bazClass) }

  disconnect() {
    // Called anytime its element is disconnected from the DOM
    // (on page change, when it's removed from or moved in the DOM, etc.)

    // Here you should remove all event listeners added in "connect()" 
    // this.fooTarget.removeEventListener('click', this._fooBar)
  }

  beforeAdd(event) {
    console.log('beforeAdd...', event.detail)
    const { button, element } = event.detail
    const entries = element.querySelectorAll('[data-siganushka-generic-collection-target="entry"]')
    if (entries.length > 4) {
      event.preventDefault()
      button.disabled = true
      alert('最多只能添加 5 个！')
    }
  }

  added(event) {
    console.log('added...', event.detail)
  }

  beforeDelete(event) {
    console.log('beforeDelete...', event.detail)
  }

  deleted(event) {
    console.log('deleted...', event.detail)
    const { element } = event.detail
    const entries = element.querySelectorAll('[data-siganushka-generic-collection-target="entry"]')
    if (entries.length < 5) {
      element.querySelector('[data-action="click->siganushka-generic-collection#add"]').disabled = false
    }
  }
}
