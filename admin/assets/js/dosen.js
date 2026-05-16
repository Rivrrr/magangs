/* ==========================================
   DOSEN - Page Specific Scripts
   ========================================== */

document.addEventListener("DOMContentLoaded", function() {

    const editBtns = document.querySelectorAll('.btn-edit');
    const editModal = document.getElementById('editModal');
    const btnTutupEdit = document.getElementById('btnTutupEdit');
    const btnBatalEdit = document.getElementById('btnBatalEdit');

    if (editBtns && editModal) {
        editBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const id = this.getAttribute("data-id");
                const nidn = this.getAttribute("data-nidn");
                const nama = this.getAttribute("data-nama");
                const status = this.getAttribute("data-status");

                document.getElementById("edit-id-dosen").value = id || "";
                document.getElementById("edit-nidn").value = nidn || "";
                document.getElementById("edit-nama").value = nama || "";
                
                if (status) {
                    document.getElementById("edit-status").value = status;
                }

                editModal.classList.add('show');
            });
        });
    }

    if (btnTutupEdit) {
        btnTutupEdit.addEventListener('click', () => editModal.classList.remove('show'));
    }

    if (btnBatalEdit) {
        btnBatalEdit.addEventListener('click', () => editModal.classList.remove('show'));
    }

    if (editModal) {
        editModal.addEventListener('click', function(e) {
            if (e.target === editModal) editModal.classList.remove('show');
        });
    }

    const deleteBtns = document.querySelectorAll(".btn-delete");
    const deleteModal = document.getElementById("deleteModal");
    const btnBatalHapus = document.getElementById("btnBatalHapus");

    if (deleteBtns && deleteModal) {
        deleteBtns.forEach((btn) => {
            btn.addEventListener("click", function (e) {
                e.preventDefault();
                
                const id = this.getAttribute("data-id");
                
                // Cek apakah input hidden ada, lalu masukkan nilainya
                const deleteInput = document.getElementById("delete-id-dosen");
                if (deleteInput) {
                    deleteInput.value = id;
                }
                
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