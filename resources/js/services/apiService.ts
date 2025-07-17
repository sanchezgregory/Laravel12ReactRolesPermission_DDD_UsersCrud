import axios from 'axios';

const apiClient = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL,
    withCredentials: true,
    withXSRFToken: true,
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    }
});

const refreshCsrfToken = async () => {
    try {
        await apiClient.get('/sanctum/csrf-cookie');
        console.log('CSRF Token refreshed:');
    } catch (error) {
        console.error('Failed to refresh CSRF token:', error);
    }
};

apiClient.interceptors.response.use(
    response => response,
    async error => {
        console.error('API Error:', error.response ? error.response.data : error.message);
        if (error.response && error.response.status === 401) {
            console.log('Unauthorized: Check your authentication');
        }
        return Promise.reject(error);
    }
);

export const api = {
    get: async (url, config = {}) => {
        try {
            return await apiClient.get(url, config);
        } catch (error) {
            console.error(`GET request to ${url} failed:`, error);
            throw error;
        }
    },
    post: async (url, data = {}, config = {}) => {
        try {
            await refreshCsrfToken();
            return await apiClient.post(url, data, config);
        } catch (error) {
            console.error(`POST request to ${url} failed:`, error);
            throw error;
        }
    },
    put: async (url, data = {}, config = {}) => {
        await refreshCsrfToken();
        return apiClient.put(url, data, config);
    },
    delete: async (url, config = {}) => {
        await refreshCsrfToken();
        return apiClient.delete(url, config);
    },
};
