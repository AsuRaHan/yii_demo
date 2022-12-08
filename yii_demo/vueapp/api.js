import axios from 'axios';

const axiosClient = axios.create({
    baseURL: '/api',
    responseType: 'json',
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
    },
});

export default {
    helpGet: url => axiosClient.get(url+window.user_access_token).then(res => res.data),
    helpPost: (url, data) => axiosClient.post(url+window.user_access_token, data).then(res => res.data),
    helpPatch: (url, data) => axiosClient.patch(url+window.user_access_token, data).then(res => res.data),
    helpDelete: (url) => axiosClient.delete(url+window.user_access_token)
}
