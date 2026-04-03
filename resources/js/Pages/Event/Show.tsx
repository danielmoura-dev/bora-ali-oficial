import React from "react";

type Event = {
    id: number;
    title: string;
    start_date: string;
    description: string;
};

type Props = {
    events: Event[];
};

export default function Show({ events }: Props) {
    return (
        <div style={{ padding: "24px" }}>
            <h1 style={{ fontSize: "24px", marginBottom: "16px" }}>
                Lista de Eventos
            </h1>

            {events.length === 0 && <p>Nenhum evento encontrado.</p>}

            <div style={{ display: "grid", gap: "16px" }}>
                {events.map((event) => (
                    <div
                        key={event.id}
                        style={{
                            border: "1px solid #ddd",
                            borderRadius: "8px",
                            padding: "16px",
                        }}
                    >
                        <h2 style={{ fontSize: "18px" }}>{event.title}</h2>

                        <p style={{ fontSize: "14px", color: "#666" }}>
                            {new Date(event.start_date).toLocaleDateString()}
                        </p>

                        <p style={{ marginTop: "8px" }}>{event.description}</p>
                    </div>
                ))}
            </div>
        </div>
    );
}
