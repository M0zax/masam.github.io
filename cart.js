let cart = JSON.parse(localStorage.getItem('cart')) || [];

function addToCart(productName, price, quantity) {
    const product = {
        name: productName,
        price: parseFloat(price),
        quantity: parseInt(quantity)
    };
    cart.push(product);
    localStorage.setItem('cart', JSON.stringify(cart));
    alert(`${productName} has been added to your cart.`);
}

function displayCart() {
    const cartContainer = document.getElementById('cart-container');
    cartContainer.innerHTML = ''; // Clear previous contents

    if (cart.length === 0) {
        cartContainer.innerHTML = '<p>Your cart is empty.</p>';
        return;
    }

    const table = document.createElement('table');
    table.innerHTML = `
        <thead>
            <tr>
                <th>Services</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            ${cart.map(item => `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.price.toFixed(2)}</td>
                    <td>${item.quantity}</td>
                    <td>${(item.price * item.quantity).toFixed(2)}</td>
                </tr>
            `).join('')}
        </tbody>
    `;
    cartContainer.appendChild(table);
}

function clearCart() {
    cart = [];
    localStorage.removeItem('cart');
    displayCart();
}

function handleFormSubmit(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const userInfo = Object.fromEntries(formData.entries());
    const order = {
        user: userInfo,
        cart: cart
    };

    fetch('submit_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(order)
    })
    .then(response => response.json())
    .then(data => {
        if (data.message === 'Order submitted successfully!') {
            alert(data.message);
            clearCart();
            event.target.reset();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error submitting order:', error);
        alert('There was an error submitting your order. Please try again.');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    displayCart();
    document.getElementById('checkout-form').addEventListener('submit', handleFormSubmit);
});
