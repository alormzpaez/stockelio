import { useForm } from "@inertiajs/react";
import { FormEventHandler } from "react";
import { RiChatNewLine } from "react-icons/ri";

export default function NewSupportChatButton() {
    const { data, setData, post, errors, processing } = useForm();

    const submitStoreChat: FormEventHandler = (e) => {
        e.preventDefault();

        post(route("chats.store"));
    };

    return (
        <form onSubmit={submitStoreChat}>
            <button
                type="submit"
                className="p-4 bg-blue-600 rounded-full cursor-pointer dark:bg-blue-700 hover:dark:bg-blue-600 hover:bg-blue-500"
            >
                <RiChatNewLine className="text-3xl text-gray-100" />
            </button>
        </form>
    );
}
