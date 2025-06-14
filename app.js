const sign_in_btm = document.querySelector("#sign-in-btn");
const sign_up_btm = document.querySelector("#sign-up-btn");
const container = document.querySelector(".container");
const sign_in_btm2= document.querySelector("#sign-in-btn2");
const sign_up_btm2 = document.querySelector("#sign-up-btn2");

sign_up_btm.addEventListener("click", () => {
    container.classList.add("sign-up-mode");
});

sign_in_btm.addEventListener("click", () => {
    container.classList.remove("sign-up-mode");
});

sign_up_btm2.addEventListener("click", () => {
    container.classList.add("sign-up-mode");
});

sign_in_btm2.addEventListener("click", () => {
    container.classList.remove("sign-up-mode");
});