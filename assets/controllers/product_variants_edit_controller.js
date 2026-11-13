import { Controller } from '@hotwired/stimulus';
import { Offcanvas } from 'bootstrap';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://symfony.com/bundles/StimulusBundle/current/index.html#lazy-stimulus-controllers
*/

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static targets = ['offcanvas']
  static values = {
    url: String,
  }

  show(event) {
    const { url, name } = event.params
    this.urlValue = url

    Offcanvas.getOrCreateInstance(this.offcanvasTarget).show()

    const title = this.offcanvasTarget.querySelector('.offcanvas-title')
    const body = this.offcanvasTarget.querySelector('.offcanvas-body')

    title.innerText = name
    body.innerHTML = `
      <div class="d-flex justify-content-center align-items-center h-100">
        <div class="spinner-border text-secondary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    `

    fetch(url).then(async response => {
      return response.ok
        ? Promise.resolve(await response.text())
        : Promise.reject(response.statusText)
    }).then(res => {
      body.innerHTML = res
    }).catch(err => {
      body.innerHTML = `<p class="alert alert-danger m-0">${err}</p>`
    })
  }

  submit(event) {
    const body = this.offcanvasTarget.querySelector('.offcanvas-body')
    const form = body.querySelector('form')
    if (!form || !this.urlValue) return

    const { currentTarget } = event
    currentTarget.disabled = true

    fetch(this.urlValue, {
      method: 'POST',
      body: new FormData(form)
    }).then(async response => {
      if (response.ok) {
        Offcanvas.getOrCreateInstance(this.offcanvasTarget).hide()
      } else {
        body.innerHTML = await response.text()
      }
    }).catch(alert).finally(() => {
      currentTarget.disabled = false
    })
  }
}
