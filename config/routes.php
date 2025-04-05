<?php
/**
 * Application Routes
 *
 * This file defines all routes for the association research and innovation application.
 */

// Get the router instance
$router = new Router();

// Home routes
$router->get('', function() {
    $controller = new HomeController();
    $controller->index();
});

$router->get('about', function() {
    $controller = new HomeController();
    $controller->about();
});

// Authentication routes
$router->get('login', function() {
    $controller = new AuthController();
    $controller->login();
});

$router->post('login', function() {
    $controller = new AuthController();
    $controller->doLogin();
});

$router->get('logout', function() {
    $controller = new AuthController();
    $controller->logout();
});

$router->get('register', function() {
    $controller = new AuthController();
    $controller->register();
});

$router->post('register', function() {
    $controller = new AuthController();
    $controller->doRegister();
});

$router->get('forgot-password', function() {
    $controller = new AuthController();
    $controller->forgotPassword();
});

$router->post('forgot-password', function() {
    $controller = new AuthController();
    $controller->doForgotPassword();
});

$router->get('reset-password', function() {
    $controller = new AuthController();
    $controller->resetPassword();
});

$router->post('reset-password', function() {
    $controller = new AuthController();
    $controller->doResetPassword();
});

// User routes
$router->get('profile', function() {
    $controller = new UserController();
    $controller->profile();
});

$router->post('profile', function() {
    $controller = new UserController();
    $controller->updateProfile();
});

$router->get('users/:id', function($id) {
    $controller = new UserController();
    $controller->view($id);
});

// Publication routes
$router->get('publications', function() {
    $controller = new PublicationController();
    $controller->index();
});

$router->get('publications/create', function() {
    $controller = new PublicationController();
    $controller->create();
});

$router->post('publications/create', function() {
    $controller = new PublicationController();
    $controller->store();
});

$router->get('publications/:id', function($id) {
    $controller = new PublicationController();
    $controller->view($id);
});

$router->get('publications/edit/:id', function($id) {
    $controller = new PublicationController();
    $controller->edit($id);
});

$router->post('publications/edit/:id', function($id) {
    $controller = new PublicationController();
    $controller->update($id);
});

$router->post('publications/delete/:id', function($id) {
    $controller = new PublicationController();
    $controller->delete($id);
});

$router->post('publications/delete-document/:id/:filename', function($id, $filename) {
    $controller = new PublicationController();
    $controller->deleteDocument($id, $filename);
});

// Event routes
$router->get('events', function() {
    $controller = new EventController();
    $controller->index();
});

$router->get('events/create', function() {
    $controller = new EventController();
    $controller->create();
});

$router->post('events/create', function() {
    $controller = new EventController();
    $controller->store();
});

// These specific routes need to come before the generic :id route
$router->get('events/seminaires', function() {
    $controller = new EventController();
    $controller->seminaires();
});

$router->get('events/conferences', function() {
    $controller = new EventController();
    $controller->conferences();
});

$router->get('events/workshops', function() {
    $controller = new EventController();
    $controller->workshops();
});

$router->get('events/search', function() {
    $controller = new EventController();
    $controller->search();
});

$router->get('events/json', function() {
    $controller = new EventController();
    $controller->getEventsJson();
});

// Generic ID routes for events
$router->get('events/:id', function($id) {
    $controller = new EventController();
    $controller->view($id);
});

$router->get('events/edit/:id', function($id) {
    $controller = new EventController();
    $controller->edit($id);
});

$router->post('events/edit/:id', function($id) {
    $controller = new EventController();
    $controller->update($id);
});

$router->post('events/delete/:id', function($id) {
    $controller = new EventController();
    $controller->delete($id);
});

$router->post('events/delete-document/:id/:filename', function($id, $filename) {
    $controller = new EventController();
    $controller->deleteDocument($id, $filename);
});

// Project routes
$router->get('projects', function() {
    $controller = new ProjectController();
    $controller->index();
});

$router->get('projects/create', function() {
    $controller = new ProjectController();
    $controller->create();
});

$router->post('projects/create', function() {
    $controller = new ProjectController();
    $controller->store();
});

$router->get('projects/:id', function($id) {
    $controller = new ProjectController();
    $controller->view($id);
});

$router->get('projects/edit/:id', function($id) {
    $controller = new ProjectController();
    $controller->edit($id);
});

$router->post('projects/edit/:id', function($id) {
    $controller = new ProjectController();
    $controller->update($id);
});

$router->post('projects/delete/:id', function($id) {
    $controller = new ProjectController();
    $controller->delete($id);
});

$router->post('projects/delete-document/:id/:filename', function($id, $filename) {
    $controller = new ProjectController();
    $controller->deleteDocument($id, $filename);
});

// News routes
$router->get('news', function() {
    $controller = new NewsController();
    $controller->index();
});

$router->get('news/create', function() {
    $controller = new NewsController();
    $controller->create();
});

$router->post('news/create', function() {
    $controller = new NewsController();
    $controller->store();
});

$router->get('news/:id', function($id) {
    $controller = new NewsController();
    $controller->view($id);
});

$router->get('news/edit/:id', function($id) {
    $controller = new NewsController();
    $controller->edit($id);
});

$router->post('news/edit/:id', function($id) {
    $controller = new NewsController();
    $controller->update($id);
});

$router->post('news/delete/:id', function($id) {
    $controller = new NewsController();
    $controller->delete($id);
});

// Contact routes
$router->get('contact', function() {
    $controller = new ContactController();
    $controller->index();
});

$router->post('contact', function() {
    $controller = new ContactController();
    $controller->send();
});

// Research Ideas routes
$router->get('ideas', function() {
    $controller = new IdeeRechercheController();
    $controller->index();
});

$router->get('ideas/create', function() {
    $controller = new IdeeRechercheController();
    $controller->create();
});

$router->post('ideas/create', function() {
    $controller = new IdeeRechercheController();
    $controller->store();
});

// Specific routes need to come before generic :id routes
$router->get('ideas/create-project/:id', function($id) {
    $controller = new IdeeRechercheController();
    $controller->createProject($id);
});

$router->post('ideas/create-project/:id', function($id) {
    $controller = new IdeeRechercheController();
    $controller->storeProject($id);
});

$router->get('ideas/:id', function($id) {
    $controller = new IdeeRechercheController();
    $controller->view($id);
});

$router->get('ideas/edit/:id', function($id) {
    $controller = new IdeeRechercheController();
    $controller->edit($id);
});

$router->post('ideas/edit/:id', function($id) {
    $controller = new IdeeRechercheController();
    $controller->update($id);
});

$router->post('ideas/update-status/:id', function($id) {
    $controller = new IdeeRechercheController();
    $controller->updateStatus($id);
});

$router->post('ideas/delete/:id', function($id) {
    $controller = new IdeeRechercheController();
    $controller->delete($id);
});

$router->post('ideas/delete-document/:id/:filename', function($id, $filename) {
    $controller = new IdeeRechercheController();
    $controller->deleteDocument($id, $filename);
});

// Admin routes
$router->get('admin', function() {
    $controller = new AdminController();
    $controller->index();
});

$router->get('admin/users', function() {
    $controller = new AdminController();
    $controller->users();
});

$router->get('admin/events', function() {
    $controller = new AdminController();
    $controller->events();
});

$router->get('admin/publications', function() {
    $controller = new AdminController();
    $controller->publications();
});

$router->get('admin/projects', function() {
    $controller = new AdminController();
    $controller->projects();
});

$router->get('admin/news', function() {
    $controller = new AdminController();
    $controller->news();
});

$router->get('admin/contacts', function() {
    $controller = new AdminController();
    $controller->contacts();
});

$router->get('admin/contacts/:id', function($id) {
    $controller = new ContactController();
    $controller->view($id);
});

$router->post('admin/contacts/:id/reply', function($id) {
    $controller = new ContactController();
    $controller->reply($id);
});

$router->post('admin/contacts/:id/delete', function($id) {
    $controller = new ContactController();
    $controller->delete($id);
});

$router->post('admin/contacts/bulk-delete', function() {
    $controller = new ContactController();
    $controller->bulkDelete();
});

$router->get('admin/settings', function() {
    $controller = new AdminController();
    $controller->settings();
});

$router->post('admin/settings', function() {
    $controller = new AdminController();
    $controller->updateSettings();
});

// Error handling - must be the last route
$router->notFound(function() {
    header('HTTP/1.1 404 Not Found');
    $view = new View();
    $view->render('errors/not_found');
});
$router->get('search', function() {
    $controller = new SearchController();
    $controller->index();
});
// Admin users management routes
$router->get('admin/users', function() {
    $controller = new UserController();
    $controller->index();
});

$router->get('admin/users/create', function() {
    $controller = new UserController();
    $controller->create();
});

$router->post('admin/users/create', function() {
    $controller = new UserController();
    $controller->store();
});

$router->get('admin/users/edit/:id', function($id) {
    $controller = new UserController();
    $controller->edit($id);
});

$router->post('admin/users/edit/:id', function($id) {
    $controller = new UserController();
    $controller->update($id);
});

$router->post('admin/users/delete/:id', function($id) {
    $controller = new UserController();
    $controller->delete($id);
});

// Add these routes to your existing routes in routes.php

// Partner routes
$router->get('partners', function() {
    $controller = new PartnerController();
    $controller->index();
});

$router->get('partners/create', function() {
    $controller = new PartnerController();
    $controller->create();
});

$router->post('partners/create', function() {
    $controller = new PartnerController();
    $controller->store();
});

$router->get('partners/:id', function($id) {
    $controller = new PartnerController();
    $controller->view($id);
});

$router->get('partners/edit/:id', function($id) {
    $controller = new PartnerController();
    $controller->edit($id);
});

$router->post('partners/edit/:id', function($id) {
    $controller = new PartnerController();
    $controller->update($id);
});

$router->post('partners/delete/:id', function($id) {
    $controller = new PartnerController();
    $controller->delete($id);
});

$router->get('events/download-document/:id/:filename', function($id, $filename) {
    $controller = new EventController();
    $controller->downloadDocument($id, $filename);
});

// For document deletion
$router->post('events/delete-document/:id/:filename', function($id, $filename) {
    $controller = new EventController();
    $controller->deleteDocument($id, $filename);
});

$router->get('publications/download/:id/:filename', function($id, $filename) {
    $controller = new PublicationController();
    $controller->downloadDocument($id, $filename);
});
$router->get('projects/download-document/:id/:filename', function($id, $filename) {
    $controller = new ProjectController();
    $controller->downloadDocument($id, $filename);
});

// Delete project document
$router->get('projects/delete-document/:id/:filename', function($id, $filename) {
    $controller = new ProjectController();
    $controller->deleteDocument($id, $filename);
});

$router->get('events/download-document/:id/:filename', function($id, $filename) {
    $controller = new EventController();
    $controller->downloadDocument($id, $filename);
});
// Return the configured router
return $router;