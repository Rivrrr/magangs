document.addEventListener("DOMContentLoaded", function () {

    // ========================
    // ELEMENTS
    // ========================
    const fileInput = document.getElementById('berkasProposal');
    const fileNameDisplay = document.getElementById('fileName');

    const formPengajuan = document.getElementById('formPengajuan');

    const radioTersedia = document.getElementById('mitraTersedia');
    const radioBaru = document.getElementById('perusahaanBaru');

    const opsiMitraTersedia = document.getElementById('opsiMitraTersedia');
    const fieldNamaPerusahaan = document.getElementById('fieldNamaPerusahaan');

    const selectMitra = document.getElementById('selectMitra');
    const inputNamaPerusahaan = document.getElementById('inputNamaPerusahaan');

    const inputAlamat = document.getElementById('inputAlamat');
    const inputProvinsi = document.getElementById('inputProvinsi');
    const inputKota = document.getElementById('inputKota');
    const inputKecamatan = document.getElementById('inputKecamatan');
    const inputKodePos = document.getElementById('inputKodePos');

    const inputsDetail = [inputAlamat, inputProvinsi, inputKota, inputKecamatan, inputKodePos];

    // ========================
    // FILE UPLOAD
    // ========================
    if (fileInput && fileNameDisplay) {
        fileInput.addEventListener('change', function () {
            if (this.files.length > 0) {
                const file = this.files[0];

                if (file.type === "application/pdf") {
                    fileNameDisplay.textContent = file.name;
                    fileNameDisplay.style.color = "#2563eb";
                } else {
                    fileNameDisplay.textContent = "Error: Harus file PDF";
                    fileNameDisplay.style.color = "#dc3545";
                    this.value = "";
                }
            }
        });
    }

    // ========================
    // AUTOFILL MITRA
    // ========================
    if (selectMitra) {
        selectMitra.addEventListener('change', function () {

            const option = this.options[this.selectedIndex];
            if (!option) return;

            inputAlamat.value = option.getAttribute('data-alamat') || '';
            inputProvinsi.value = option.getAttribute('data-provinsi') || '';
            inputKota.value = option.getAttribute('data-kota') || '';
            inputKecamatan.value = option.getAttribute('data-kecamatan') || '';
            inputKodePos.value = option.getAttribute('data-kodepos') || '';
        });
    }

    // ========================
    // TOGGLE JENIS PERUSAHAAN
    // ========================
    function toggleJenisPerusahaan() {
        if (radioTersedia && radioTersedia.checked) {

            opsiMitraTersedia.style.display = 'flex';
            fieldNamaPerusahaan.style.display = 'none';

            selectMitra.required = true;
            inputNamaPerusahaan.required = false;

            inputsDetail.forEach(el => {
                el.readOnly = true;
                el.style.backgroundColor = '#f8fafc';
                el.style.color = '#64748b';
            });

            // trigger autofill if already selected
            if (selectMitra.value) {
                selectMitra.dispatchEvent(new Event('change'));
            }

        } else {

            opsiMitraTersedia.style.display = 'none';
            fieldNamaPerusahaan.style.display = 'block';

            selectMitra.required = false;
            inputNamaPerusahaan.required = true;

            inputsDetail.forEach(el => {
                el.readOnly = false;
                el.style.backgroundColor = '#fff';
                el.style.color = '#1e293b';
                el.value = '';
            });

            inputNamaPerusahaan.value = '';
        }
    }

    // ========================
    // EVENTS
    // ========================
    if (radioTersedia) radioTersedia.addEventListener('change', toggleJenisPerusahaan);
    if (radioBaru) radioBaru.addEventListener('change', toggleJenisPerusahaan);

    // ========================
    // INIT
    // ========================
    toggleJenisPerusahaan();
});