import axios from "axios";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

window.Echo = new Echo({
    broadcaster: "pusher",
    key: process.env.MIX_PUSHER_APP_CLUSTER,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true,
    authEndpoint: "/broadcasting/auth",
});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import "./echo";
