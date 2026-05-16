const detailModal = document.getElementById("detailModal");
const btnTutupDetail = document.getElementById("btnTutupDetail");
const btnBatalDetail = document.getElementById("btnBatalDetail");
let currentIdx = null;

function openDetail(idx) {
  const d = dataPengajuan[idx];

  currentIdx = idx;

  document.getElementById("det-id").textContent = d.id;
  document.getElementById("det-tanggal").textContent = d.tanggal;

  const statusEl = document.getElementById("det-status");

  statusEl.textContent = d.status;

  statusEl.style.color =
    d.status === "Disetujui"
      ? "#198754"
      : d.status === "Ditolak"
        ? "#dc3545"
        : "#d97706";

  document.getElementById("det-judul").textContent = d.judul_proposal;
  document.getElementById("det-nim").textContent = d.nim;
  document.getElementById("det-nama").textContent = d.nama;
  document.getElementById("det-prodi").textContent = d.prodi;
  document.getElementById("det-hp").textContent = d.no_hp;
  document.getElementById("det-email").textContent = d.email;
  document.getElementById("det-perusahaan").textContent = d.perusahaan;
  document.getElementById("det-alamat").textContent = d.alamat_perusahaan;
  document.getElementById("det-provinsi").textContent = d.provinsi;
  document.getElementById("det-kota").textContent = d.kota;
  document.getElementById("det-kecamatan").textContent = d.kecamatan;
  document.getElementById("det-kodepos").textContent = d.kode_pos;
  document.getElementById("det-bidang").textContent = d.bidang_magang;
  document.getElementById("det-mulai").textContent = d.tgl_mulai;
  document.getElementById("det-selesai").textContent = d.tgl_selesai;
  document.getElementById("det-catatan").textContent = d.catatan;
  document.getElementById("det-berkas").textContent = d.berkas;
  document.getElementById("det-berkas-link").href = "../uploads/" + d.berkas;

  const dosenText = document.getElementById("det-dosen-text");
  const dosenAssign = document.getElementById("det-dosen-assign");
  const dosenSelect = document.getElementById("det-dosen-select");

  if (d.status === "Menunggu") {
    dosenText.parentElement.style.display = "none";

    dosenAssign.style.display = "flex";

    dosenSelect.value = d.dosen_pembimbing || "";
  } else {
    dosenText.parentElement.style.display = "flex";

    dosenText.textContent = d.dosen_pembimbing || "-";

    dosenAssign.style.display = "none";
  }

  const btnApprove = document.getElementById("btnApproveDetail");
  const btnReject = document.getElementById("btnRejectDetail");

  if (d.status === "Menunggu") {
    btnApprove.style.display = "inline-block";
    btnReject.style.display = "inline-block";

    btnApprove.dataset.id = d.id;
    btnReject.dataset.id = d.id;
  } else {
    btnApprove.style.display = "none";
    btnReject.style.display = "none";
  }

  detailModal.classList.add("show");
}

document.querySelectorAll(".btn-detail").forEach((btn) => {
  btn.addEventListener("click", function (e) {
    e.preventDefault();

    openDetail(parseInt(this.dataset.idx));
  });
});

btnTutupDetail.addEventListener("click", () => {
  detailModal.classList.remove("show");
});

btnBatalDetail.addEventListener("click", () => {
  detailModal.classList.remove("show");
});

detailModal.addEventListener("click", function (e) {
  if (e.target === detailModal) {
    detailModal.classList.remove("show");
  }
});

const approveModal = document.getElementById("approveModal");
const btnBatalApprove = document.getElementById("btnBatalApprove");
const btnConfirmApprove = document.getElementById("btnConfirmApprove");

let selectedApproveId = null;

document.querySelectorAll(".btn-approve").forEach((btn) => {
  btn.addEventListener("click", function (e) {
    e.preventDefault();

    selectedApproveId = this.dataset.id;

    approveModal.classList.add("show");
  });
});

document
  .getElementById("btnApproveDetail")
  .addEventListener("click", function () {
    detailModal.classList.remove("show");

    selectedApproveId = this.dataset.id;

    approveModal.classList.add("show");
  });

btnBatalApprove.addEventListener("click", () => {
  approveModal.classList.remove("show");
});

btnConfirmApprove.addEventListener("click", () => {

  const idDosen =
    document.getElementById("det-dosen-select").value;

  if (!idDosen) {
    alert("Pilih dosen pembimbing terlebih dahulu.");
    return;
  }

  window.location.href =
    "pengajuan_action.php?id=" +
    selectedApproveId +
    "&action=approve&id_dosen=" +
    idDosen;

});

approveModal.addEventListener("click", function (e) {
  if (e.target === approveModal) {
    approveModal.classList.remove("show");
  }
});

const rejectModal = document.getElementById("rejectModal");
const btnBatalReject = document.getElementById("btnBatalReject");
const btnConfirmReject = document.getElementById("btnConfirmReject");

let selectedRejectId = null;

// FROM TABLE BUTTON
document.querySelectorAll(".btn-reject").forEach((btn) => {
  btn.addEventListener("click", function (e) {
    e.preventDefault();

    selectedRejectId = this.dataset.id;

    rejectModal.classList.add("show");
  });
});

document
  .getElementById("btnRejectDetail")
  .addEventListener("click", function () {
    detailModal.classList.remove("show");

    selectedRejectId = this.dataset.id;

    rejectModal.classList.add("show");
  });

btnBatalReject.addEventListener("click", () => {
  rejectModal.classList.remove("show");
});

btnConfirmReject.addEventListener("click", () => {
  window.location.href =
    "pengajuan_action.php?id=" + selectedRejectId + "&action=reject";
});

rejectModal.addEventListener("click", function (e) {
  if (e.target === rejectModal) {
    rejectModal.classList.remove("show");
  }
});

document.querySelector(".form-select").addEventListener("change", function () {
  const selected = this.value.toLowerCase();
  const rows = document.querySelectorAll("tbody tr");

  rows.forEach((row) => {
    const statusEl = row.querySelector(".badge-status");
    if (!statusEl) return;

    const rowStatus = statusEl.textContent.trim().toLowerCase();

    if (selected === "" || rowStatus === selected) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
});

document.getElementById("filterStatus").addEventListener("change", function () {
  const selected = this.value.toLowerCase();
  const rows = document.querySelectorAll("tbody tr");

  rows.forEach((row) => {
    const statusEl = row.querySelector(".badge-status");
    if (!statusEl) return;

    const rowStatus = statusEl.textContent.trim().toLowerCase();

    if (selected === "" || rowStatus === selected) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
});
