let search = document.querySelector('.search-box');
    document.querySelector('#search-icon').onclick = () => {
    search.classList.toggle('active');
}

let user = document.querySelector('.user-box');
    document.querySelector('#user-icon').onclick = () => {
    user.classList.toggle('active');
}

let header = document.querySelector('header');
window.addEventListener('scroll', () => {
    header.classList.toggle('active', window.scrolly > 0);
})