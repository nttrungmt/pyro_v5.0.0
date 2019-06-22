# Right Wall Hugger: Roomba, Only using the Bump Sensors

from pyrobot.brain import Brain  
import time
   
class WallHuggerRoomba(Brain):

   # Give the sensors, decide the next move  
   def determineMove(self, left, right):

	if ((left == 0) and (right == 0)) :
		self.robot.move(.5,.1)
	else : 
		self.robot.move(-1,1)

   def step(self):
   	left = self.robot.getSensor("leftBump")
	right = self.robot.getSensor("rightBump")
	self.determineMove(left, right)  

def INIT(engine):  
	assert (engine.robot.requires("continuous-movement"))
	return WallHuggerRoomba('WallHuggerRoomba', engine)
