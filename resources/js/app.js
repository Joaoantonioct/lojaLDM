import './catalog-carousel.js';

const category = document.querySelector('.nav-category');
const categoryToggle = document.querySelector('.nav-category-toggle');

if (category && categoryToggle) {
	let closeTimer;

	categoryToggle.addEventListener('click', function (event) {
		event.preventDefault();
		clearTimeout(closeTimer);
		category.classList.add('is-open');
	});

	category.addEventListener('mouseenter', function () {
		clearTimeout(closeTimer);
	});

	category.addEventListener('mouseleave', function () {
		closeTimer = setTimeout(function () {
			category.classList.remove('is-open');
			categoryToggle.blur();
		}, 250);
	});

	document.addEventListener('click', function (event) {
		if (!category.contains(event.target)) {
			category.classList.remove('is-open');
			categoryToggle.blur();
		}
	});

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape') {
			category.classList.remove('is-open');
			categoryToggle.blur();
		}
	});
}