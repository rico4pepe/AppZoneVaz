import { apiFetch } from '../utils/api';
import Swal from 'sweetalert2';


export default function useFeedInteraction(token) {
    const interact = async ({ contentId, answer }) => {
        try {
            const data = await apiFetch('/feed/interact', {
                method: 'POST',
                body: JSON.stringify({
                    content_id: contentId,
                    answer: answer
                })
            });

            return data.data;

        } catch (e) {
            Swal.fire('Error', e.message, 'error');
            throw e;
        }
    };

    return { interact };
}