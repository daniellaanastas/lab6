 import React from 'react';
 import './CourseCss.css';

function Course({ cname, isConfirmed, onConfirm, onEdit, onDelete }) {
  return (
    <div className="Course">
      <span>{cname}</span>
      <div className="button-container">
        {isConfirmed === 1 ? (
          <>
            <button disabled className="edit">Edit</button>
            <button disabled className="delete">Delete</button>
            <button disabled className="confirm">Confirmed</button>
          </>
        ) : (
          <>
            <button onClick={() => onEdit(cname)} className="edit">Edit</button>
            <button onClick={() => onDelete(cname)} className="delete">Delete</button>
            <button onClick={() => onConfirm(cname)} className="confirm">Confirm</button>
          </>
        )}
      </div>
    </div>
  );
}


 export default Course;
