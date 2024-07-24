import { Chat, Message } from "@/types"
import axios from "axios"
import { useEffect, useRef, useState } from "react"

const useChat = ({ 
    chat,
    onSendMessage,
}: { 
    chat: Chat,
    onSendMessage: () => void
}): {
    messages: Message[] | null,
    getInitialMessages: () => void,
    getNextMessages: () => void,
    sendMessage: (body: string) => void,
    areThereMessagesForLoad: boolean,
    isTyping: boolean,
    isInChat: boolean,
    typing: () => void
} => {
    const [isTyping, setIsTyping] = useState(false)
    const [isInChat, setIsInChat] = useState(false)
    const timeoutRef = useRef<NodeJS.Timeout|null>(null)
    const [messages, setMessages] = useState<Message[] | null>(null)
    const [nextCursor, setNextCursor] = useState<string | null>(null)

    const typing = () => {
        (window as any).Echo.private(`App.Models.Chat.${chat.id}`).whisper("typing")
    }

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
    }

    const getInitialMessages = () => {
        getMessages(null)
    }

    const getNextMessages = () => {
        getMessages(nextCursor)
    }

    const sendMessage = (body: string) => {
        axios
            .post(
                `${window.location.origin}/api/v1/chats/${chat.id}/messages`,
                {
                    body,
                }, {
                    headers: {
                        'X-Socket-ID': (window as any).Echo.socketId()
                    }
                }
            )
            .then((response) => {
                onSendMessage()
                getInitialMessages()
            })
            .catch((e) => {
                // console.log('error: ', e);
            });
    }

    useEffect(() => {
        // Connection for 'in chat' and new message functionality
        (window as any).Echo.join(`App.Models.Chat.${chat.id}`)
            .here((users: { id: number, name: string }[]) => {
                if (users.some((user) => user.id == chat.receiver.id)) {
                    setIsInChat(true)
                }
            })
            .joining((user: { id: number, name: string }) => {
                if (user.id == chat.receiver.id) {
                    setIsInChat(true)
                }
            })
            .leaving((user: { id: number, name: string }) => {
                if (user.id == chat.receiver.id) {
                    setIsInChat(false)
                }
            })
            .listen('NewMessage', () => {
                getInitialMessages()
            })
            .error((error: any) => {
                // console.error(error);
            });

        // Connection for typing functionality
        (window as any).Echo.private(`App.Models.Chat.${chat.id}`)
            .listenForWhisper("typing", () => {
                setIsTyping(true)

                if (timeoutRef.current) {
                    clearTimeout(timeoutRef.current)
                }

                timeoutRef.current = setTimeout(() => {
                    setIsTyping(false)
                }, 3000)
            });

        return () => {
            (window as any).Echo.leave(`App.Models.Chat.${chat.id}`)
        }
    }, [])

    return {
        messages, 
        getInitialMessages, 
        getNextMessages, 
        sendMessage,
        areThereMessagesForLoad: !!nextCursor,
        isTyping, 
        isInChat, 
        typing
    }
}

export default useChat