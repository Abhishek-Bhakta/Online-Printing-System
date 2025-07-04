document.addEventListener("DOMContentLoaded", function () {
    const map = L.map('map').setView([19.19230, 72.84677], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);

    const shops = JSON.parse(document.getElementById('shop-data').textContent);
    const shopSelect = document.getElementById('shop_id');

    function updateShopDescription(shopId) {
        const selectedShop = shops.find(shop => shop.shop_id == shopId);

        if (selectedShop) {
            document.getElementById('shop-name').textContent = selectedShop.shop_name;
            document.getElementById('shopkeeper-name').textContent = selectedShop.shopkeeper_name;
            document.getElementById('shop-address').textContent = selectedShop.shop_address;
            document.getElementById('price-bw').textContent = selectedShop.price_bw;
            document.getElementById('price-color').textContent = selectedShop.price_color;
            map.setView([selectedShop.latitude, selectedShop.longitude], 15);
        }
    }

    shops.forEach(shop => {
        const pin = L.marker([shop.latitude, shop.longitude]).addTo(map);
        pin.bindPopup(`<strong>${shop.shop_name}</strong><br>Status: ${shop.status}`);

        pin.on('click', () => {
            if (shop.status === 'offline') {
                alert("This shop is offline, please choose another shop.");
                return;
            }
            shopSelect.value = shop.shop_id;
            updateShopDescription(shop.shop_id);
            validateForm();
        });

        if (shop.status === 'online') {
            const option = document.createElement('option');
            option.value = shop.shop_id;
            option.textContent = shop.shop_name;
            shopSelect.appendChild(option);
        }
    });

    shopSelect.addEventListener('change', function () {
        updateShopDescription(this.value);
        validateForm();
    });

    if (shops.length > 0) {
        updateShopDescription(shops[0].shop_id);
    }

    document.getElementById('pay-btn').addEventListener('click', async function () {
        const shopId = document.getElementById('shop_id').value;
        const copies = parseInt(document.getElementById('copies').value);
        const size = document.getElementById('size').value;
        const pages = parseInt(document.getElementById('pages').value);
        const color = document.getElementById('color').value;
        const documentFile = document.getElementById('document').files[0];
    
        if (!shopId || !copies || !size || !color || !documentFile) {
            alert("Please fill all fields before proceeding to payment.");
            return;
        }
    
        const selectedShop = shops.find(shop => shop.shop_id == shopId);
        if (!selectedShop) {
            alert("Invalid shop selection.");
            return;
        }
    
        const formData = new FormData();
        formData.append('document', documentFile);
        formData.append('shopId', shopId);
        formData.append('copies', copies);
        formData.append('pages', pages);
        formData.append('size', size);
        formData.append('color', color);
    
        const response = await fetch('razorpay_payment.php', {
            method: 'POST',
            body: formData
        });
    
        const data = await response.json();
    
        if (data.error) {
            alert(data.error);
            return;
        }
    
        const numberOfPages = data.pages;
    
        const pricePerCopy = (color === 'color') ? parseFloat(selectedShop.price_color) : parseFloat(selectedShop.price_bw);
        const totalAmount = pricePerCopy * pages * copies * 100; 
    
        var options = {
            "key": "<?= $razorpay_key; ?>",
            "amount": totalAmount,
            "currency": "INR",
            "name": selectedShop.shop_name,
            "description": `Print Order - ${selectedShop.shopkeeper_name}`,
            "order_id": data.order_id,
            "handler": function (response) {
                const paymentIdInput = document.getElementById('payment_id');
                const orderIdInput = document.getElementById('order_id');
                const verificationCodeInput = document.getElementById('verification_code');
                
                // Check if the elements exist before setting their values
                if (paymentIdInput) paymentIdInput.value = response.razorpay_payment_id;
                if (orderIdInput) orderIdInput.value = data.order_id;
                if (verificationCodeInput) verificationCodeInput.value = data.verification_code;
    
                // Submit the form manually
                document.getElementById('uploadForm').submit();
            },
            "prefill": {
                "email": "customer@example.com",
                "contact": "9999999999"
            },
            "theme": {
                "color": "#3399cc"
            }
        };
    
        var rzp1 = new Razorpay(options);
        rzp1.open();
    });
    
    
    
    // Set the default page count to 1 when the page loads
    document.getElementById('pages').value = 1;
    document.getElementById('page-count-display').style.display = 'block'; // Ensure it's visible
    document.getElementById('page-count-value').textContent = 0; // Display the default page count as 1
    
    // Detect when a document is selected
    document.getElementById('document').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const pageCountField = document.getElementById('pages');
        const pageCountDisplay = document.getElementById('page-count-display');
        const pageCountValue = document.getElementById('page-count-value');
        
        // If the document is a PDF, count pages
        if (file && file.type === 'application/pdf') {
            const reader = new FileReader();
    
            reader.onload = function(e) {
                const pdfData = e.target.result;
    
                // Use PDF.js to count pages
                const loadingTask = pdfjsLib.getDocument(pdfData);
                loadingTask.promise.then(function(pdf) {
                    const numPages = pdf.numPages;
                    
                    // Set the page count in the hidden field and display it
                    pageCountField.value = numPages;
                    pageCountDisplay.style.display = 'block'; // Make page count visible
                    pageCountValue.textContent = numPages; // Display the page count
                }).catch(function(error) {
                    console.error("Error loading PDF: ", error);
                });
            };
    
            // Read the file as ArrayBuffer for PDF.js
            reader.readAsArrayBuffer(file);
        } else {
            // If the document is not a PDF, keep the page count as 1
            pageCountField.value = 1;
            pageCountDisplay.style.display = 'block'; // Make page count visible
            pageCountValue.textContent = 1; // Display 1 as page count
        }
    });
        // Validate form and enable/disable the payment button
        function validateForm() {
            const shopId = document.getElementById('shop_id').value;
            const documentField = document.getElementById('document').files.length; // document file length
            const copies = document.getElementById('copies').value;
            const size = document.getElementById('size').value;
            const color = document.getElementById('color').value;
    
            const payButton = document.getElementById('pay-btn');
    
            // Validate each field
            const isValidCopies = copies >= 1 && !isNaN(copies);
            const isValidSize = size === "A4" || size === "A3";
            const isValidColor = color === "color" || color === "bw";
    
            // Enable payment button if all fields are valid
            if (shopId && documentField > 0 && isValidCopies && isValidSize && isValidColor) {
                payButton.disabled = false;
            } else {
                payButton.disabled = true;
            }
        }
    
        // Enable payment button on form input change
        document.getElementById('uploadForm').addEventListener('input', validateForm);
        document.getElementById('document').addEventListener('change', validateForm); // For document field
        document.getElementById('copies').addEventListener('input', validateForm);
        document.getElementById('size').addEventListener('change', validateForm);
        document.getElementById('color').addEventListener('change', validateForm);
    });
window.onload = function() {
    let formSection = document.querySelector('.form-section');
    let shopInfo = document.querySelector('.shop-info');

    let maxHeight = Math.max(formSection.clientHeight, shopInfo.clientHeight);
    
    formSection.style.height = maxHeight + "px";
    shopInfo.style.height = maxHeight + "px";
};
function sortDocuments() {
    let sortType = document.getElementById("sort").value;
    let documentList = document.getElementById("document-list");
    let cards = Array.from(documentList.getElementsByClassName("document-card"));

    cards.sort((a, b) => {
        let dateA = new Date(a.getAttribute("data-date"));
        let dateB = new Date(b.getAttribute("data-date"));

        if (sortType === "date-desc") {
            return dateB - dateA; // Newest First
        } else if (sortType === "date-asc") {
            return dateA - dateB; // Oldest First
        } else if (sortType === "day") {
            return dateA.getDay() - dateB.getDay(); // Sort by Day
        } else if (sortType === "month") {
            return dateA.getMonth() - dateB.getMonth(); // Sort by Month
        }
    });

    // Re-add sorted cards
    documentList.innerHTML = "";
    cards.forEach(card => documentList.appendChild(card));
}