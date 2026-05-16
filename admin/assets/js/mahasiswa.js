/* ==========================================
   MAHASISWA - Page Specific Scripts
   ========================================== */

document.addEventListener("DOMContentLoaded", function() {
    
    // Edit Modal Logic
    const editButtons = document.querySelectorAll(".btn-edit");
    const editModal = document.getElementById("editModal");
    const btnTutupEdit = document.getElementById("btnTutupEdit");
    const btnBatalEdit = document.getElementById("btnBatalEdit");

    if (editButtons && editModal) {
        editButtons.forEach((button) => {
            button.addEventListener("click", function (e) {
                e.preventDefault();
                
                editModal.classList.add("show");

                console.log("Data from button:", this.dataset);

                document.getElementById("edit-id").value = this.dataset.id || "";
                document.getElementById("edit-nim").value = this.dataset.nim || "";
                document.getElementById("edit-nama").value = this.dataset.nama || "";
                document.getElementById("edit-tempat").value = this.dataset.tempat || "";
                document.getElementById("edit-kota").value = this.dataset.kota || "";
                document.getElementById("edit-status").value = this.dataset.status || "";
                document.getElementById("edit-dosen").value = this.dataset.dosen || "";
            });
        });
    }

    if (btnTutupEdit) {
        btnTutupEdit.addEventListener("click", () => editModal.classList.remove("show"));
    }

    if (btnBatalEdit) {
        btnBatalEdit.addEventListener("click", () => editModal.classList.remove("show"));
    }

    if (editModal) {
        editModal.addEventListener("click", function (e) {
            if (e.target === editModal) {
                editModal.classList.remove("show");
            }
        });
    }

// Delete Modal Logic
    const deleteBtns = document.querySelectorAll(".btn-delete");
    const deleteModal = document.getElementById("deleteModal");
    const btnBatalHapus = document.getElementById("btnBatalHapus");

    if (deleteBtns && deleteModal) {
        deleteBtns.forEach((btn) => {
            btn.addEventListener("click", function (e) {
                e.preventDefault();
                
                // Grab the ID from the clicked trash icon
                const id = this.getAttribute("data-id");
                
                // Put it into the hidden form input
                document.getElementById("delete-id").value = id;
                
                // Show modal
                deleteModal.classList.add("show");
            });
        });
    }

    if (btnBatalHapus) {
        btnBatalHapus.addEventListener("click", () => deleteModal.classList.remove("show"));
    }

    if (deleteModal) {
        deleteModal.addEventListener("click", function (e) {
            if (e.target === deleteModal) {
                deleteModal.classList.remove("show");
            }
        });
    }
});