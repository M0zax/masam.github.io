function addToCart(productName, category, price, quantity) {
    let cartItems = localStorage.getItem('cartItems');
    cartItems = cartItems ? JSON.parse(cartItems) : {};

    const itemKey = productName.toLowerCase().replace(/\s+/g, '-');
    const totalPrice = price * quantity;
    if (cartItems[itemKey]) {
        cartItems[itemKey].quantity += quantity;
        cartItems[itemKey].totalPrice += totalPrice;
    } else {
        cartItems[itemKey] = {
            productName: productName,
            category: category,
            price: price,
            quantity: quantity,
            totalPrice: totalPrice
        };
    }

    localStorage.setItem('cartItems', JSON.stringify(cartItems));
    alert(`${quantity} ${productName} added to cart!`);
    window.location.href = 'shopping-cart.html';
}

function populateCart() {
    let cartItems = localStorage.getItem('cartItems');
    cartItems = cartItems ? JSON.parse(cartItems) : {};

    const categories = {};

    for (let key in cartItems) {
        const item = cartItems[key];
        if (!categories[item.category]) {
            categories[item.category] = [];
        }
        categories[item.category].push(item);
    }

    const cartContainer = document.getElementById('cart-container');
    cartContainer.innerHTML = ''; // Clear existing content

    for (let category in categories) {
        const table = document.createElement('table');
        table.border = "1";

        const caption = document.createElement('caption');
        caption.textContent = category;
        table.appendChild(caption);

        const thead = document.createElement('thead');
        thead.innerHTML = `
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        `;
        table.appendChild(thead);

        const tbody = document.createElement('tbody');
        let categoryTotal = 0;
        categories[category].forEach(item => {
            categoryTotal += item.totalPrice;
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.productName}</td>
                <td>$${item.price.toFixed(2)}</td>
                <td>${item.quantity}</td>
                <td>$${item.totalPrice.toFixed(2)}</td>
            `;
            tbody.appendChild(row);
        });

        const totalRow = document.createElement('tr');
        totalRow.innerHTML = `
            <td colspan="3"><strong>Total</strong></td>
            <td><strong>$${categoryTotal.toFixed(2)}</strong></td>
        `;
        tbody.appendChild(totalRow);

        table.appendChild(tbody);
        cartContainer.appendChild(table);
    }
}

function clearCart() {
    localStorage.removeItem('cartItems');
    populateCart();
}

// Call populateCart on window load to ensure the cart is populated when the page loads
window.onload = populateCart;
