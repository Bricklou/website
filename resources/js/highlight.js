import Prism from "prismjs";

document.body.classList.add("line-numbers");

// if you are intending to use Prism functions manually, you will need to set:
Prism.manual = true;

const els = document.querySelectorAll(
    ':not(.ck-content) code[class*="language-"]'
);

for (const el of els) {
    el.classList.add("not-prose");
    Prism.highlightElement(el);
}
