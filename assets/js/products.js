document.addEventListener("DOMContentLoaded", () => {
    fetch('api/get_product.php')
        // แก้จาก Response เป็น response (ตัวพิมพ์เล็ก)
        .then(response => response.json()) 
        .then(products => {
            const container = document.getElementById('product-container');
            container.innerHTML = '';

            products.forEach(product => {
                const formattedPrice = new Intl.NumberFormat('th-TH', {
                    style: 'currency',
                    currency: 'THB'           
                }).format(product.price);

                // --- ส่วนจัดการรูปภาพที่เพิ่มเข้ามา ---
                let imageSrc = product.image || '';

                // ถ้าใน DB มีแค่ชื่อไฟล์ (เช่น MA4000X.png) ให้เติม Path โฟลเดอร์ให้อัตโนมัติ
                if (imageSrc && !imageSrc.startsWith('http') && !imageSrc.startsWith('assets/')) {
                    imageSrc = `assets/images/products/${imageSrc}`;
                }

                // ถ้าไม่มีข้อมูลรูป หรือรูปหาไม่พบ ให้ใช้รูปสำรอง (Fallback)
                if (!imageSrc) {
                    imageSrc = 'assets/images/no-image.jpg';
                }
                // ----------------------------------

                const cardHTML = `
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0">
                            <!-- เพิ่ม onerror ดักจับถ้ารูปในเครื่องพัง ให้ดึงรูปสำรองมาแสดงแทน -->
                            <img src="${imageSrc}" 
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/300x220?text=No+Image';" 
                                 class="card-img-top p-3 img-fluid" 
                                 style="height: 220px; object-fit: contain;" 
                                 alt="${product.title}">
                            <div class="card-body d-flex flex-column text-center p-4">
                                <span class="badge bg-secondary mb-2 mx-auto">${product.category}</span>
                                <h5 class="card-title fw-bold text-primary mb-2">${product.title}</h5>
                                <p class="card-text text-muted small mb-3">${product.description || ''}</p>
                                <h4 class="text-danger fw-bold mb-3">${formattedPrice}</h4>
                                <a href="contact.html" class="btn btn-primary w-100 mt-auto">ติดต่อสั่งซื้อ</a>
                            </div>
                        </div>
                    </div>
                `;
                container.innerHTML += cardHTML;
            });
        })
        .catch(error => console.error('Error:', error));
});