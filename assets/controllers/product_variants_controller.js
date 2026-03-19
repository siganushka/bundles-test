import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://symfony.com/bundles/StimulusBundle/current/index.html#lazy-stimulus-controllers
*/

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static targets = ['modal']
  static values = {
    url: String,
  }

  show(event) {
    const { url, name } = event.params
    this.urlValue = url

    const modalTitle = this.modalTarget.querySelector('.modal-title')
    const modalBody = this.modalTarget.querySelector('.modal-body')

    modalTitle.innerText = name
    modalBody.innerHTML = `
      <div class="d-flex justify-content-center">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    `

    const modal = Modal.getOrCreateInstance(this.modalTarget)
    modal.show(event.currentTarget)

    fetch(url).then(async response => {
      return response.ok
        ? Promise.resolve(await response.text())
        : Promise.reject(response.statusText)
    }).then(res => {
      modalBody.innerHTML = res
    }).catch(err => alert(err))
  }

  submit(event) {
    if (!this.urlValue) return

    const modalBody = this.modalTarget.querySelector('.modal-body')
    const modalForm = modalBody.querySelector('form')
    if (!modalForm) return

    event.target.disabled = true
    fetch(this.urlValue, {
      method: 'POST',
      body: new FormData(modalForm)
    }).then(async response => {
      modalBody.innerHTML = await response.text()
    }).finally(() => {
      event.target.disabled = false
    })
  }
}
