import './catalog-carousel.js';
import './cart-header.js';

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

const mobileMenu = document.querySelector('.mobile-menu');

if (mobileMenu) {
	document.addEventListener('pointerdown', function (event) {
		if (!mobileMenu.contains(event.target)) {
			mobileMenu.open = false;
		}
	});

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape') {
			mobileMenu.open = false;
		}
	});
}

const accountDropdown = document.querySelector('.nav-actions .account-dropdown');

if (accountDropdown) {
	let closeTimer;
	const accountToggle = accountDropdown.querySelector('summary');

	accountDropdown.addEventListener('mouseenter', function () {
		clearTimeout(closeTimer);
	});

	accountDropdown.addEventListener('mouseleave', function () {
		closeTimer = setTimeout(function () {
			accountDropdown.open = false;

			if (accountDropdown.contains(document.activeElement)) {
				document.activeElement.blur();
			}
		}, 500);
	});

	accountDropdown.addEventListener('keydown', function (event) {
		if (event.key === 'Escape') {
			clearTimeout(closeTimer);
			accountDropdown.open = false;
			accountToggle?.blur();
		}
	});
}