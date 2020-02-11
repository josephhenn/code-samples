library(glmnet)
data = as.matrix(read.csv(file.choose(), header = T))
train = sample(1:1030, 1030*.8, replace = F)
y = scale(data[,9])
x = scale(data[,-9])

###Ridge
ridge = cv.glmnet(x[train,], y[train], family = "gaussian", alpha = 0, intercept = FALSE)
lambda = ridge$lambda.min
lambda
coef.ridge = matrix(coef(ridge, s = lambda))[2:9]
coef.ridge
ridge = glmnet(x[train,], y[train], family = "gaussian", alpha = 0, intercept = FALSE)
y_ridge = predict(ridge, x[-train,], s = lambda)
mse_ridge = sum((y[-train]-y_ridge)^2)/length(y[-train])
mse_ridge


###LASSO
lasso = cv.glmnet(x[train,], y[train], family = "gaussian", alpha = 1, intercept = FALSE)
lambda = lasso$lambda.min
lambda
coef.lasso = matrix(coef(lasso, s = lambda))[2:9]
coef.lasso
lasso = glmnet(x[train,], y[train], family = "gaussian", alpha = 1, intercept = FALSE)
plot(lasso, xvar = "lambda", label = TRUE)
abline(v = log(lambda))
y_lasso = predict(lasso, x[-train,], s = lambda)
mse_lasso = sum((y[-train]-y_lasso)^2)/length(y[-train])
mse_lasso

###Adaptive LASSO
gamma = 2
b.ols = solve(t(x[train,])%*%x[train,])%*%t(x[train,])%*%y[train]
w1 = 1/abs(b.ols)^gamma
w2 = 1/abs(coef.ridge)^gamma
alasso1 = cv.glmnet(x[train,], y[train], family = "gaussian", alpha = 1, intercept = FALSE, penalty.factor = w1)
alasso2 = cv.glmnet(x[train,], y[train], family = "gaussian", alpha = 1, intercept = FALSE, penalty.factor = w2)
lambda1 = alasso1$lambda.min
lambda2 = alasso2$lambda.min
lambda1
lambda2
coef.alasso1 = matrix(coef(alasso1, s = lambda1))[2:9]
coef.alasso2 = matrix(coef(alasso2, s = lambda2))[2:9]
alasso1 = glmnet(x[train,], y[train], family = "gaussian", alpha = 1, intercept = FALSE, penalty.factor = w1)
alasso2 = glmnet(x[train,], y[train], family = "gaussian", alpha = 1, intercept = FALSE, penalty.factor = w2)
plot(alasso1, xvar = "lambda", label = TRUE)
abline(v=log(lambda1))
plot(alasso2, xvar = "lambda", label = TRUE)
abline(v=log(lambda2))
View(cbind.data.frame(b.ols, coef.ridge, coef.lasso, coef.alasso1, coef.alasso2))
y_alasso1 = predict(alasso1, x[-train,], s = lambda1)
mse_alasso1 = sum((y[-train]-y_alasso1)^2)/length(y[-train])
y_alasso2 = predict(alasso2, x[-train,], s = lambda2)
mse_alasso2 = sum((y[-train]-y_alasso2)^2)/length(y[-train])
mse_alasso1
mse_alasso2

###ENet
alphas = seq(0,1,length=50)
best.alpha = 0
lambda.enet = 0
min.mse = Inf
for (i in alphas) {
  enet = cv.glmnet(x[train,], y[train], family = "gaussian", alpha = i, intercept = FALSE)
  templambda = enet$lambda.min
  enet = glmnet(x[train,], y[train], family = "gaussian", alpha = i, intercept = FALSE)
  y_enet = predict(enet, x[-train,], s = templambda)
  mse_enet = sum((y[-train]-y_enet)^2)/length(y[-train])
  if(mse_enet < min.mse){
    best.alpha = i
    lambda.enet = templambda
    min.mse = mse_enet
  }
}
best.alpha
lambda.enet
min.mse
enet = glmnet(x[train,], y[train], family = "gaussian", alpha = best.alpha, intercept = FALSE)
coef.enet = matrix(coef(enet, s = lambda.enet))[2:9]
coef.enet
