import React from 'react';
import "./History.css"

const History = ({ todos }) => {
  return (
    <div>
      <h1>Todo History</h1>
      <ul>
        {todos.map((item) => (
          <li className="hist" key={item.id}>
            <strong>{item.todo}</strong> - Added on: {item.createdAt}
          </li>
        ))}
      </ul>
    </div>
  );
};

export default History;
