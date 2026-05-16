/* ==========================================
   ADMIN SHARED SCRIPTS
   Digunakan di semua halaman admin
   ========================================== */

// Profile dropdown toggle
const profileToggle = document.getElementById("profileToggle");
const profileDropdown = document.getElementById("profileDropdown");

profileToggle.addEventListener("click", function (e) {
  e.stopPropagation();
  profileDropdown.classList.toggle("show");
});

document.addEventListener("click", function () {
  profileDropdown.classList.remove("show");
});

// Logout modal
const btnKeluar = document.getElementById("btnKeluar");
const logoutModal = document.getElementById("logoutModal");
const btnBatal = document.getElementById("btnBatal");

btnKeluar.addEventListener("click", function (e) {
  e.preventDefault();
  profileDropdown.classList.remove("show");
  logoutModal.classList.add("show");
});

btnBatal.addEventListener("click", function () {
  logoutModal.classList.remove("show");
});

logoutModal.addEventListener("click", function (e) {
  if (e.target === logoutModal) {
    logoutModal.classList.remove("show");
  }
});

// Profile modal
const btnProfil = document.getElementById("btnProfil");
const profileModal = document.getElementById("profileModal");
const btnTutupProfil = document.getElementById("btnTutupProfil");

btnProfil.addEventListener("click", function (e) {
  e.preventDefault();
  profileDropdown.classList.remove("show");
  profileModal.classList.add("show");
});

btnTutupProfil.addEventListener("click", function () {
  profileModal.classList.remove("show");
});

profileModal.addEventListener("click", function (e) {
  if (e.target === profileModal) {
    profileModal.classList.remove("show");
  }
});

const editButtons = document.querySelectorAll(".btn-edit");

const editModal = document.getElementById("editModal");

editButtons.forEach((button) => {
  button.addEventListener("click", function (e) {
    e.preventDefault();

    editModal.classList.add("show");

    document.getElementById("edit-id-dosen").value = this.dataset.id;

    document.getElementById("edit-nidn").value = this.dataset.nidn;

    document.getElementById("edit-nama").value = this.dataset.nama;

    document.getElementById("edit-status").value = this.dataset.status;
  });
});

const btnTutupEdit = document.getElementById("btnTutupEdit");

btnTutupEdit.addEventListener("click", () => {
  editModal.classList.remove("show");
});

const btnBatalEdit = document.getElementById("btnBatalEdit");

btnBatalEdit.addEventListener("click", () => {
  editModal.classList.remove("show");
});

editModal.addEventListener("click", function (e) {
  if (e.target === editModal) {
    editModal.classList.remove("show");
  }
});
