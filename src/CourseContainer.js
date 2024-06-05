import React, { useState, useEffect } from 'react';
import axios from 'axios';
import Course from './Course';



function CourseContainer() {
  const [courses, setCourses] = useState([]);

  useEffect(() => {
    fetchCourses();
  }, []);

  async function fetchCourses() {
    try {
        const response = await axios.get('http://localhost/course.php');
        setCourses(response.data);
    } catch (error) {
        console.error('Error fetching courses:', error);
    }
}

async function addCourse() {
    const courseName = prompt("Enter course name");
    if (courseName) {
        try {
            const response = await axios.post('http://localhost/saveCourses.php', { cname: courseName, isConfirmed: 0 });
            setCourses([...courses, response.data]);
        } catch (error) {
            console.error('Error adding course:', error);
        }
    }
}

  async function editCourse(course) {
    const newName = prompt("Enter new course name", course.text);
    if (newName) {
      try {
        const response = await axios.post('http://localhost/course.php', { action: 'edit', old_text: course.text, new_text: newName });
        const updatedCourses = courses.map(c => (c.id === course.id ? response.data : c));
        setCourses(updatedCourses);
      } catch (error) {
        console.error('Error editing course:', error);
      }
    }
  }

  async function deleteCourse(course) {
    if (window.confirm("Are you sure you want to delete this course?")) {
      try {
        await axios.post('http://localhost/course.php', { action: 'delete', text: course.text });
        setCourses(courses.filter(c => c.text !== course.text));
      } catch (error) {
        console.error('Error deleting course:', error);
      }
    }
  }

  async function confirmCourse(course) {
    if (course.confirmed) {
      alert("This course is already confirmed.");
      return;
    }
    if (window.confirm("Are you sure you want to confirm?")) {
      try {
        await axios.post('http://localhost/course.php', { action: 'confirm', text: course.text, confirmed: true });
        const updatedCourses = courses.map(c => (c.text === course.text ? { ...c, confirmed: true } : c));
        setCourses(updatedCourses);
      } catch (error) {
        console.error('Error confirming course:', error);
      }
    }
  }

  return (
    <div className="courses-container">
      <div>
        <button onClick={addCourse}>Add Course</button>
      </div>
      {courses.map(course => (
        <Course
          key={course.id}
          course={course}
          onEdit={editCourse}
          onDelete={deleteCourse}
          onConfirm={confirmCourse}
        />
      ))}
    </div>
  );
}

export default CourseContainer;
