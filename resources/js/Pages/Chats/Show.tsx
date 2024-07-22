import Message from "@/Components/Message";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Chat, Message as MessageType, PageProps } from "@/types";
import { Head, router } from "@inertiajs/react";
import { useIntersectionObserver } from "@uidotdev/usehooks";
import axios from "axios";
import { Avatar, Button, Textarea } from "flowbite-react";
import { FormEventHandler, ReactElement, useEffect, useState } from "react";
import { FaArrowLeft, FaSpinner } from "react-icons/fa";
import { IoMdSend } from "react-icons/io";

function Show({
    auth,
    chat,
}: PageProps<{
    chat: Chat;
}>) {
    const [body, setBody] = useState('');
    const [ref, entry] = useIntersectionObserver({
        threshold: 0,
        root: null,
        rootMargin: "0px",
    });
    const [nextCursor, setNextCursor] = useState(null);
    const [messages, setMessages] = useState<MessageType[] | null>(null);

    const getMessages = (cursor: string | null) => {
        axios
            .get(
                `${window.location.origin}/api/v1/chats/${chat.id}/messages?cursor=${cursor}`
            )
            .then((response) => {
                // response.data has props: data, links and meta
                setNextCursor(response.data.meta.next_cursor);

                if (cursor) {
                    setMessages((prevMessages) => [
                        ...(prevMessages ?? []),
                        ...response.data.data,
                    ]);
                } else {
                    setMessages(response.data.data);
                }
            })
            .catch((e) => {
                // console.log(e);
            });
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        axios
            .post(
                `${window.location.origin}/api/v1/chats/${chat.id}/messages`,
                {
                    body,
                }
            )
            .then((response) => {
                setBody('')
                getMessages(null)
            })
            .catch((e) => {
                // console.log('error: ', e);
            });
    };

    const onScrollEnd = () => {
        getMessages(nextCursor);
    };

    const onBeginning = () => {
        getMessages(null);
    };

    useEffect(() => {
        if (entry?.isIntersecting) {
            onScrollEnd();
        }
    }, [entry?.isIntersecting]);

    useEffect(() => {
        onBeginning();
    }, []);

    return (
        <>
            <Head title="Chat" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg md:px-4">
                        <div className="flex items-center gap-3 p-6 text-4xl font-medium text-gray-500 dark:text-gray-100">
                            <Button
                                color="gray"
                                onClick={() => {
                                    router.visit(route("dashboard"));
                                }}
                                // disabled={processing}
                            >
                                <div className="flex items-center gap-2">
                                    <FaArrowLeft className="" />
                                </div>
                            </Button>
                            <Avatar>
                                <div className="text-base font-medium dark:text-white">
                                    <div>{chat.receiver.name}</div>
                                    <div className="text-sm text-gray-500 dark:text-gray-400">
                                        En el chat
                                    </div>
                                </div>
                            </Avatar>
                        </div>
                        <hr className="h-px bg-gray-200 border-0 dark:bg-gray-700"></hr>
                        <section className="max-w-2xl px-5 py-8 mx-auto antialiased bg-white dark:bg-gray-800">
                            <div className="flex flex-col max-w-screen-xl gap-6 mx-auto 2xl:px-0">
                                <div className="flex flex-col-reverse w-full gap-2 overflow-auto max-h-72 md:max-h-96 min-h-56 lg:gap-4">
                                    {!messages ? (
                                        <div className="flex justify-center">
                                            <div className="flex justify-center py-4">
                                                <svg
                                                    className="w-5 h-5 mr-3 animate-spin"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <FaSpinner className="text-2xl text-gray-400" />
                                                </svg>
                                            </div>
                                        </div>
                                    ) : messages.length == 0 ? (
                                        <div className="flex justify-center">
                                            <div className="flex justify-center w-full py-2">
                                                <span className="text-sm text-gray-900 dark:text-gray-100">
                                                    Sin mensajes aún
                                                </span>
                                            </div>
                                        </div>
                                    ) : (
                                        <>
                                            {messages.map(
                                                (
                                                    message: MessageType,
                                                    index
                                                ) => (
                                                    <Message
                                                        currentUser={auth.user}
                                                        message={message}
                                                    />
                                                )
                                            )}
                                            {nextCursor && (
                                                <div
                                                    ref={ref}
                                                    className="flex justify-center"
                                                >
                                                    <div className="flex justify-center py-4">
                                                        <svg
                                                            className="w-5 h-5 mr-3 animate-spin"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <FaSpinner className="text-2xl text-gray-400" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            )}
                                        </>
                                    )}
                                </div>

                                <form onSubmit={submit}>
                                    <div className="flex items-start w-full gap-2">
                                        <Textarea
                                            placeholder="Escribe un mensaje..."
                                            required
                                            rows={4}
                                            value={body}
                                            onChange={(e) =>
                                                setBody(e.target.value)
                                            }
                                        />
                                        <Button
                                            type="submit"
                                            color="gray"
                                            // disabled={processing}
                                        >
                                            <div className="flex items-center gap-2">
                                                <IoMdSend className="text-lg" />
                                            </div>
                                        </Button>
                                    </div>
                                </form>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </>
    );
}

Show.layout = (page: ReactElement<PageProps>) => (
    <AuthenticatedLayout user={page.props.auth.user} children={page} />
);

export default Show;
