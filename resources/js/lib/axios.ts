import axios from 'axios';

const instance = axios.create({
    baseURL: window.location.origin,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    },
    withCredentials: true, // Important for Keycloak session handling
});

// Add a request interceptor to ensure HTTPS
instance.interceptors.request.use((config) => {
    // Ensure the URL uses HTTPS
    if (config.url && !config.url.startsWith('http')) {
        config.url = window.location.origin + config.url;
    }
    // Get the CSRF token from the meta tag
    const token = document.head.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token;
    }
    return config;
});

// Add response interceptor to handle authentication errors
instance.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            // Redirect to Keycloak login
            window.location.href = '/auth/keycloak';
            return Promise.reject(error);
        }
        return Promise.reject(error);
    }
);

export default instance; 