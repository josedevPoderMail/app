import React, { useEffect, useState } from "react";
import axios from "axios";
import { Config } from "./components/config";

export const Turnos = () => {
  const [shifts, setShifts] = useState([]);

  useEffect(() => {
    axios.get("https://rol.podermail.org/api/shifts").then((response) => {
      setShifts(response.data);
    });
  }, []);

  const markAsCompleted = (id) => {
    axios.patch("https://rol.podermail.org/api/shifts", { id }).then(() => {
      setShifts((prevShifts) =>
        prevShifts.map((shift) =>
          shift.id === id ? { ...shift, status: "cumplido" } : shift
        )
      );
    });
  };
  const dayNow = () => {
    let date = Date.now();

    return new Date(date).toISOString().split("T")[0];
  };
  return (
    <div className="p-6 flex flex-col  bg-white rounded-md shadow-md h-screen w-full mx-auto">
      {/* Título */}
      <div className="h-auto ">
        <h1 className="text-2xl font-bold text-gray-700 mb-4 text-center">
        Rol de limpieza (baño)
        </h1>
      </div>

      {/* Lista de turnos */}
      <ul className="space-y-4  overflow-y-auto w-full">
        {shifts.map((shift) => (
          <li
            key={shift.id}
            className={`p-4 ${ shift.date === dayNow()
              ? "bg-green-100" 
              : shift.date < dayNow() && shift.status === "pendiente"
              ? "bg-orange-100" 
              : "bg-gray-100" } border border-gray-200 rounded-md shadow-sm flex justify-between items-center`}
          >
            {/* Información del turno */}
            <div className="text-gray-800">
              <p>
                <span className="font-semibold">Fecha:</span> {shift.date}
              </p>
              <p>
                <span className="font-semibold">Usuario:</span>{" "}
                {shift.user_name}
              </p>
              <p>
                <span
                  className={`font-semibold ${
                    shift.status === "pendiente"
                      ? "text-yellow-600"
                      : "text-green-600"
                  }`}
                >
                  Estado:
                </span>{" "}
                {shift.status}
              </p>
            </div>

            {/* Botón "Completar" */}
            {shift.status === "pendiente" && (
              <button
                disabled={ shift.date === dayNow()
                  ? false 
                  : shift.date < dayNow() && shift.status === "pendiente"
                  ? true
                  : true}
                onClick={() => markAsCompleted(shift.id)}
                className={`ml-4 ${
                  shift.date === dayNow()
                    ? "bg-green-500" 
                    : shift.date < dayNow() && shift.status === "pendiente"
                    ? "bg-orange-500" 
                    : "bg-gray-500" 
                } text-white font-semibold py-2 px-4 rounded  transition`}
              >
                {
                  shift.date === dayNow()
                    ? "Completar" 
                    : shift.date < dayNow() && shift.status === "pendiente"
                    ? "No marcado" 
                    : "No disponible" 
                }
              </button>
            )}
          </li>
        ))}
      </ul>
      <div>
        <p className="py-5 text-center text-blue-500 font-bold text-xl" >
          Created by josé
        </p>
      </div>
      {/* <Config /> */}
    </div>
  );
};
