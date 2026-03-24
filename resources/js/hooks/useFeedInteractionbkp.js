import Swal from 'sweetalert2';

export default function useFeedInteraction(token) {
    const API = '/fanzone/api';

    const interact = async ({ contentId, answer }) => {
        try {
            const res = await fetch(`${API}/feed/interact`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                },
                body: JSON.stringify({
                    content_id: contentId,
                    answer: answer
                })
            });

            const data = await res.json();

            if (!res.ok) {
                throw new Error(data.message || 'Action failed');
            }

            // ✅ Backend already normalized
            return data.data;

        } catch (e) {
            Swal.fire('Error', e.message, 'error');
            throw e;
        }
    };

    return { interact };
}