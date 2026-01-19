const popupOverlay = document.getElementById('popupOverlay');
const popupModal = document.getElementById('popupModal');
const closePopup = document.getElementById('closePopup');

function showPopup(title, message) {
    document.getElementById('popupTitle').innerText = title;
    document.getElementById('popupMessage').innerText = message;
    popupOverlay.classList.remove('hidden');
    setTimeout(() => popupModal.classList.add('opacity-100', 'scale-100'), 10);
}

function hidePopup() {
    popupModal.classList.remove('opacity-100', 'scale-100');
    setTimeout(() => popupOverlay.classList.add('hidden'), 200);
}

closePopup.addEventListener('click', hidePopup);
