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

    const offcanvas = Offcanvas.getOrCreateInstance(this.offcanvasTarget)
    offcanvas.show()

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
    if (!this.urlValue) return

    const body = this.offcanvasTarget.querySelector('.offcanvas-body')
    const form = body.querySelector('form')
    if (!form) return

    event.target.disabled = true
    fetch(this.urlValue, {
      method: 'POST',
      body: new FormData(form)
    }).then(async response => {
      body.innerHTML = await response.text()
    }).catch(alert).finally(() => {
      event.target.disabled = false
    })
  }
}
