//************** START NAVBAR AMINATE ***************/

const menuHamburger = document.querySelector(".menu-btn")
const navLinks = document.querySelector(".nav-links")

menuHamburger.addEventListener('click', ()=>{
    navLinks.classList.toggle("mobile-menu")
})
//************** END NAVBAR AMINATE ***************/

//**************** START AMINATE BURGER **********/

const menuBtn = document.querySelector('.menu-btn');
let menuOpen = false;
menuBtn.addEventListener('click', () => {
    if(!menuOpen) {
        menuBtn.classList.add('open');
        menuOpen =true;
    } else {
        menuBtn.classList.remove('open');
        menuOpen =false;
    }
});

//************** END AMINATE BURGER **********/

//************ START SLIDER TESTIMONIAL ************/

$('.owl-carousel').owlCarousel({
    autoplay: true,
    smartSpeed: 1000,
    margin: 50,
    dots: true,
    loop: true,
    nav : false,
    navText : [
        '<i class="bi bi-arrow-left"></i>',
        '<i class="bi bi-arrow-right"></i>'
    ],
    responsive:{
        0:{
            items:1
        },
        600:{
            items:1
        },
        1000:{
            items:2
        }
    }
})

//************ END SLIDER TESTIMONIAL ************/
