import React, { Component } from 'react';
import "./Navbar.css"

const Navbar = ({ setView }) => {
  return (
    <nav className="flex justify-between items-center p-4 bg-gray-200 shadow-md">
      <div className="logo">
        <span className="font-bold text-xl">Tasky</span>
      </div>
      <ul className="flex space-x-4">
        <li
          className="cursor-pointer hover:font-bold transition duration-150"
          onClick={() => setView('home')}
        >
          Home
        </li>
        <li
          className="cursor-pointer hover:font-bold transition duration-150"
          onClick={() => setView('history')}
        >
          History
        </li>
      </ul>
    </nav>
  );
};

export default Navbar;
