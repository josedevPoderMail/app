import React, { useEffect, useState } from "react";
import axios from "axios";

export const Config = () => {
  const [config, setConfig] = useState({ cleaning_interval: 0 });
 
  const generateShifts = async () => {
    try {
      const response = await axios.post("https://rol.podermail.org/api/shifts/generate", {
        start_date: "2024-12-25",
        end_date: "2025-03-25",
      });
      alert(response.data.message);

    } catch (error) {
      console.error("Error:", error.response?.data || error.message);
    }
  };
  
   
  
  // Obtener configuración actual
  useEffect(() => {
    axios.get("https://rol.podermail.org/api/config").then((response) => {
      setConfig(response.data);
    });
  }, []);

  // Actualizar configuración
  const updateConfig = () => {
    axios.put("https://rol.podermail.org/api/config", config).then((e) => {
      let res = e.data;
      console.log(res);
      
      alert("Configuración actualizada correctamente.");
    });
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setConfig({ ...config, [name]: value });
  };

  return (
    <div className="p-6 bg-gray-100 rounded-md shadow-md max-w-md mx-auto">
  {/* Botón para generar la lista */}
  <button
    
    onClick={generateShifts}
    className="w-full bg-gray-500 text-white font-semibold py-2 px-4 rounded hover:bg-gray-600 transition"
  >
    Generar lista
  </button>

  {/* Título */}
  <h1 className="text-2xl font-bold text-gray-700 mt-4">Configuración</h1>

  {/* Campo de entrada para intervalo */}
  <div className="mt-4">
    <label className="block text-gray-600 font-medium mb-2">
      Intervalo de limpieza (días):
    </label>
    <input
      type="number"
      name="cleaning_interval"
      value={config.cleaning_interval}
      onChange={handleInputChange}
      className="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
    />
  </div>

  {/* Botón para guardar configuración */}
  <button
   
    onClick={updateConfig}
    className="w-full bg-gray-500 text-white font-semibold py-2 px-4 rounded mt-4 hover:bg-gray-600 transition"
  >
    Guardar
  </button>
</div>

  );
};

