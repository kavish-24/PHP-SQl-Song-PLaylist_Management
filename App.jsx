import { useState, useEffect } from 'react';
import './App.css';
import Navbar from './Component/Navbar';
import { v4 as uuidv4 } from 'uuid';
import History from './Component/History';

function App() {
  const [todo, setTodo] = useState("");
  const [todos, setTodos] = useState([]);
  const [showFinished, setShowFinished] = useState(false);
  const [isEditing, setIsEditing] = useState(false);
  const [currentId, setCurrentId] = useState(null);
  const [view, setView] = useState('home'); // Manage current view

  useEffect(() => {
    const todoString = localStorage.getItem("todos");
    if (todoString) {
      const storedTodos = JSON.parse(todoString);
      setTodos(storedTodos);
    }
  }, []);

  const saveToLocalStorage = (items) => {
    localStorage.setItem("todos", JSON.stringify(items));
  };

  const handleAdd = () => {
    if (todo.trim() === "") return;

    if (isEditing) {
      const updatedTodos = todos.map((item) =>
        item.id === currentId ? { ...item, todo } : item
      );
      setTodos(updatedTodos);
      saveToLocalStorage(updatedTodos);
      setIsEditing(false);
      setCurrentId(null);
    } else {
      const newTodo = { 
        id: uuidv4(), 
        todo, 
        isCompleted: false, 
        createdAt: new Date().toLocaleString() 
      };
      const updatedTodos = [...todos, newTodo];
      setTodos(updatedTodos);
      saveToLocalStorage(updatedTodos);
    }
    setTodo("");
  };

  const handleChange = (e) => {
    setTodo(e.target.value);
  };

  const handleDelete = (id) => {
    const updatedTodos = todos.filter((item) => item.id !== id);
    setTodos(updatedTodos);
    saveToLocalStorage(updatedTodos);
  };

  const handleEdit = (id) => {
    const currentTodo = todos.find((item) => item.id === id);
    setTodo(currentTodo.todo);
    setIsEditing(true);
    setCurrentId(id);
  };

  const toggleFinished = () => {
    setShowFinished(!showFinished);
  };

  return (
    <div className="App">
      <Navbar setView={setView} /> {/* Pass setView to Navbar */}

      {view === 'home' && (
        <div className='main'>
          <h1>Todo List</h1>
          <input
            type="text"
            value={todo}
            onChange={handleChange}
            placeholder="Enter your task..."
          />
          <button onClick={handleAdd}>{isEditing ? "Update" : "Add"}</button>

          <label>
            Show Finished
            <input
              type="checkbox"
              onChange={toggleFinished}
              checked={showFinished}
            />
          </label>

          <ul>
            {todos
              .filter((item) => (showFinished ? item.isCompleted : !item.isCompleted))
              .map((item) => (
                <li key={item.id}>
                  <input
                    type="checkbox"
                    checked={item.isCompleted}
                    onChange={() => {
                      const updatedTodos = todos.map((todo) =>
                        todo.id === item.id
                          ? { ...todo, isCompleted: !todo.isCompleted }
                          : todo
                      );
                      setTodos(updatedTodos);
                      saveToLocalStorage(updatedTodos);
                    }}
                  />
                  <span
                    style={{
                      textDecoration: item.isCompleted ? "line-through" : "none",
                    }}
                  >
                    {item.todo}
                  </span>
                  <button onClick={() => handleEdit(item.id)}>Edit</button>
                  <button onClick={() => handleDelete(item.id)}>Delete</button>
                </li>
              ))}
          </ul>
        </div>
      )}

      {view === 'history' && <History todos={todos} />}
    </div>
  );
}

export default App;
