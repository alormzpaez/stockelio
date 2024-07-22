import { Message as MessageType, User } from "@/types";

export default function Message({
    currentUser,
    message,
}: {
    currentUser: User|null;
    message: MessageType;
}) {
    return (
        <>
            {currentUser && currentUser.id == message.user_id ? (
                <div className="flex justify-end">
                    <div className="flex flex-col w-full max-w-[320px] leading-1.5 p-4 gap-1 bg-gray-100 rounded-s-xl rounded-ee-xl dark:bg-gray-700">
                        <p className="text-sm font-normal text-gray-900 dark:text-white">
                            {message.body}
                        </p>
                        <span className="text-sm font-normal text-gray-500 dark:text-gray-400">
                            Sent: {message.created_at}
                        </span>
                    </div>
                </div>
            ) : currentUser && currentUser.id != message.user_id ? (
                <div className="">
                    <div className="flex flex-col w-full max-w-[320px] leading-1.5 p-4 gap-1 border-gray-200 bg-gray-100 rounded-e-xl rounded-es-xl dark:bg-gray-700">
                        <p className="text-sm font-normal text-gray-900 dark:text-white">
                            {message.body}
                        </p>
                        <span className="text-sm font-normal text-gray-500 dark:text-gray-400">
                            Sent: {message.created_at}
                        </span>
                    </div>
                </div>
            ) : null}
        </>
    );
}
