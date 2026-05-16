// Profile dropdown toggle
const profileToggle = document.getElementById('profileToggle');
const profileDropdown = document.getElementById('profileDropdown');

if (profileToggle && profileDropdown) {
    profileToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        profileDropdown.classList.toggle('show');
    });
    document.addEventListener('click', function() {
        profileDropdown.classList.remove('show');
    });
}

// Logout modal
const btnKeluar = document.getElementById('btnKeluar');
const logoutModal = document.getElementById('logoutModal');
const btnBatal = document.getElementById('btnBatal');

if (btnKeluar && logoutModal) {
    btnKeluar.addEventListener('click', function(e) {
        e.preventDefault();
        if(profileDropdown) profileDropdown.classList.remove('show');
        logoutModal.classList.add('show');
    });
}
if (btnBatal && logoutModal) {
    btnBatal.addEventListener('click', function() { logoutModal.classList.remove('show'); });
}
if (logoutModal) {
    logoutModal.addEventListener('click', function(e) {
        if (e.target === logoutModal) logoutModal.classList.remove('show');
    });
}

// Profile modal
const btnProfil = document.getElementById('btnProfil');
const profileModal = document.getElementById('profileModal');
const btnTutupProfil = document.getElementById('btnTutupProfil');

if (btnProfil && profileModal) {
    btnProfil.addEventListener('click', function(e) {
        e.preventDefault();
        if(profileDropdown) profileDropdown.classList.remove('show');
        profileModal.classList.add('show');
    });
}
if (btnTutupProfil && profileModal) {
    btnTutupProfil.addEventListener('click', function() { profileModal.classList.remove('show'); });
}
if (profileModal) {
    profileModal.addEventListener('click', function(e) {
        if (e.target === profileModal) profileModal.classList.remove('show');
    });
}
