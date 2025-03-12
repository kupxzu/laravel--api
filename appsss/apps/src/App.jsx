import React, { useState, useEffect } from 'react';
import axios from 'axios';

function DataComponent() {
 // Ensure data defaults to an empty array
 const [data, setData] = useState([]);
 const [loading, setLoading] = useState(true);
 const [error, setError] = useState(null);
 

 useEffect(() => {
  const apiUrl = 'http://127.0.0.1:8000/api/employees';

  const fetchData = async () => {
    try {
      const response = await axios.get(apiUrl, {
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        withCredentials: false
      });

      console.log('API Response:', response.data);

      // Update state to match the API structure
      setData(response.data.data || []); // Access "data" from the response
    } catch (err) {
      setError('Failed to fetch data: ' + err.message);
      console.error('Detailed error:', err);
    } finally {
      setLoading(false);
    }
  };

  fetchData();
}, []);


if (loading) return <div>Loading data...</div>;
if (error) return <div>Error: {error}</div>;

return (
  <div className="data-container">
    <h2>Employee List</h2>
    <table className="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>First Name</th>
          <th>Last Name</th>
        </tr>
      </thead>
      <tbody>
  {Array.isArray(data) && data.length > 0 ? (
    data.map((employee) => (
      <tr key={employee.id}>
        <td>{employee.id}</td>
        <td>{employee.first_name} </td>
        <td>{employee.last_name}</td>
        {/* <td>{employee.created_at}</td>
        <td>{employee.updated_at}</td> */}
      </tr>
    ))
  ) : (
    <tr>
      <td colSpan="4">No data available</td>
    </tr>
  )}
</tbody>

    </table>
  </div>
);

}

export default DataComponent;