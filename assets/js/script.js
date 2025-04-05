window.onload = () => {
    const form = document.querySelector('#caption_form')
    const baseUrl = form.getAttribute('data-base-url')
    const locationField = form.querySelector('input[type="hidden"]')
    const captionField = form.querySelector('input[type="text"]')
    const numFields = form.querySelectorAll('input[type="range"]')
    const allFields = form.querySelectorAll('input:not([type="submit"])')
    const image = document.querySelector('#preview img')
    const constructLink = () => {
        let values = [
            encodeURIComponent(locationField.value.trim()),
            encodeURIComponent(captionField.value.trim()=='' ? 'Your caption' : captionField.value.trim())
        ]
        numFields.forEach(field => {
            values.push(encodeURIComponent(field.value.trim()))
        })
        const url = baseUrl + values.join("/")
        return url
    }
    const updateImage = () => {
        image.src = constructLink()
    }
    updateImage()

    let updateTimer
    allFields.forEach(field=>{
        field.addEventListener('input', () => {
            clearTimeout(updateTimer)
            updateTimer = setTimeout(() => {
                updateImage()
            }, 500)
        })
    })
};