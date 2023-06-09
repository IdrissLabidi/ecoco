import {loadStripe} from '@stripe/stripe-js';

export async function checkout(publicKey, sessionId) {
    const stripe = await loadStripe(publicKey);
    const {error} = await stripe.redirectToCheckout({
        sessionId: sessionId,
    });

    if (error) {
        alert(error.message);
    }
}