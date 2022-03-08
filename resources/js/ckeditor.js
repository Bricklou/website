import CKEditor from "ckeditor5-custom-build/build/ckeditor";

const uploaderConfig = {
    uploadUrl: '/admin/systems/files',
    withCredentials: true,
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf_token"]').getAttribute('content')
    }
}

function run() {
    const editorEls = document.getElementsByClassName("ckeditor");

    if (editorEls) {
        for (let el of editorEls) {
            CKEditor.create(el, {
                simpleUpload: uploaderConfig
            }).catch(e => console.error(e))
        }
    }
};

window.onload = run
document.addEventListener("turbo:load", run)

